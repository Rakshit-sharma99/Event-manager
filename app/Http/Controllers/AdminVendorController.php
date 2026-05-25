<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Vendor;
use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminVendorController extends Controller
{
    /**
     * List all vendors with search & filter.
     */
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($nested) => $nested
                ->where('business_name', 'like', "%{$q}%")
                ->orWhere('name', 'like', "%{$q}%")
                ->orWhere('contact_email', 'like', "%{$q}%")
                ->orWhere('contact_number', 'like', "%{$q}%"));
        }

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $vendors = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $categories = Vendor::all()->pluck('category')->filter()->unique()->sort()->values();

        return view('admin.vendors.index', compact('vendors', 'categories'));
    }

    /**
     * List vendors pending verification.
     */
    public function verifications(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        $validTabs = ['pending', 'under_review', 'approved', 'rejected'];
        if (!in_array($tab, $validTabs)) $tab = 'pending';

        $query = Vendor::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($nested) => $nested
                ->where('business_name', 'like', "%{$q}%")
                ->orWhere('name', 'like', "%{$q}%")
                ->orWhere('contact_email', 'like', "%{$q}%"));
        }

        $query->where('verification_status', $tab);

        $vendors = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Count badges for tabs
        $counts = [
            'pending' => Vendor::where('verification_status', 'pending')->count(),
            'under_review' => Vendor::where('verification_status', 'under_review')->count(),
            'approved' => Vendor::where('verification_status', 'approved')->count(),
            'rejected' => Vendor::where('verification_status', 'rejected')->count(),
        ];

        return view('admin.vendor-verifications', compact('vendors', 'tab', 'counts'));
    }

    /**
     * Show vendor detail page.
     */
    public function show(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $user = $vendor->user;
        $documents = VendorDocument::where('vendor_id', $id)->get();
        $bookings = Booking::where('vendor_id', $id)->orderByDesc('created_at')->limit(20)->get();
        $auditLogs = AuditLog::where('target_type', 'vendor')
            ->where('target_id', $id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('admin.vendors.show', compact('vendor', 'user', 'documents', 'bookings', 'auditLogs'));
    }

    /**
     * Approve a vendor.
     */
    public function approve(Request $request, string $id)
    {
        $vendor = Vendor::findOrFail($id);

        $vendor->update([
            'verification_status' => 'approved',
            'is_verified' => true,
            'is_active' => true,
            'verified_at' => now(),
            'verified_by' => (string) $request->user()->getKey(),
            'rejection_reason' => null,
        ]);

        // Update admin notes if provided
        if ($request->filled('admin_notes')) {
            $vendor->update(['admin_notes' => $request->input('admin_notes')]);
        }

        AuditLog::log('vendor_approved', 'vendor', $id, [
            'business_name' => $vendor->business_name,
        ]);

        // Send approval email
        try {
            $vendorUser = $vendor->user;
            if ($vendorUser) {
                \Illuminate\Support\Facades\Mail::to($vendorUser->email)
                    ->send(new \App\Mail\VendorApprovedMail($vendor, $vendorUser));
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed to send vendor approval email: ' . $e->getMessage());
        }

        return back()->with('success', "Vendor '{$vendor->business_name}' has been approved and activated.");
    }

    /**
     * Reject a vendor.
     */
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $vendor = Vendor::findOrFail($id);

        $vendor->update([
            'verification_status' => 'rejected',
            'is_verified' => false,
            'is_active' => false,
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        if ($request->filled('admin_notes')) {
            $vendor->update(['admin_notes' => $request->input('admin_notes')]);
        }

        AuditLog::log('vendor_rejected', 'vendor', $id, [
            'business_name' => $vendor->business_name,
            'reason' => $request->input('rejection_reason'),
        ]);

        // Send rejection email
        try {
            $vendorUser = $vendor->user;
            if ($vendorUser) {
                \Illuminate\Support\Facades\Mail::to($vendorUser->email)
                    ->send(new \App\Mail\VendorRejectedMail($vendor, $vendorUser, $request->input('rejection_reason')));
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed to send vendor rejection email: ' . $e->getMessage());
        }

        return back()->with('success', "Vendor '{$vendor->business_name}' has been rejected.");
    }

    /**
     * Suspend a vendor (keep verified but deactivate).
     */
    public function suspend(Request $request, string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['is_active' => false]);

        AuditLog::log('vendor_suspended', 'vendor', $id, [
            'business_name' => $vendor->business_name,
        ]);

        return back()->with('success', "Vendor '{$vendor->business_name}' has been suspended.");
    }

    /**
     * Re-activate a vendor.
     */
    public function activate(Request $request, string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['is_active' => true]);

        AuditLog::log('vendor_activated', 'vendor', $id, [
            'business_name' => $vendor->business_name,
        ]);

        return back()->with('success', "Vendor '{$vendor->business_name}' has been activated.");
    }

    /**
     * Securely stream a private vendor document to admin browser.
     */
    public function viewDocument(string $vendorId, string $docId)
    {
        $doc = VendorDocument::where('_id', $docId)
            ->where('vendor_id', $vendorId)
            ->firstOrFail();

        $fullPath = storage_path("app/private/{$doc->file_path}");

        if (!file_exists($fullPath)) {
            abort(404, 'Document file not found.');
        }

        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $doc->original_filename . '"',
        ]);
    }
}
