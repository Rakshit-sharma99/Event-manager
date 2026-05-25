<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorDocumentController extends Controller
{
    /**
     * Show document upload page for vendor.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $vendor = $this->activeVendor($request);

        $documents = $vendor ? VendorDocument::where('vendor_id', (string) $vendor->getKey())->get()->keyBy('document_type') : collect();

        $documentTypes = [
            'govt_id' => ['label' => 'Government ID', 'required' => true, 'icon' => '🪪'],
            'pan' => ['label' => 'PAN Card', 'required' => true, 'icon' => '💳'],
            'aadhaar' => ['label' => 'Aadhaar Card', 'required' => true, 'icon' => '🆔'],
            'gst' => ['label' => 'GST Certificate', 'required' => false, 'icon' => '📋'],
            'business_license' => ['label' => 'Business License', 'required' => false, 'icon' => '📜'],
        ];

        return view('vendors.dashboard.documents', compact('user', 'vendor', 'documents', 'documentTypes'));
    }

    /**
     * Upload a verification document.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'document_type' => ['required', 'string', 'in:govt_id,pan,aadhaar,gst,business_license'],
            'document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $vendor = $this->activeVendor($request);

        if (!$vendor) {
            return back()->with('error', 'Please create a business profile first.');
        }

        $docType = $request->input('document_type');
        $file = $request->file('document');
        $vendorId = (string) $vendor->getKey();

        // Store in private storage (not publicly accessible)
        $filename = $docType . '_' . Str::random(20) . '.' . $file->getClientOriginalExtension();
        $path = "vendor-documents/{$vendorId}/{$filename}";
        Storage::disk('local')->put("private/{$path}", file_get_contents($file->getRealPath()));

        // Delete old document of same type if exists
        $existing = VendorDocument::where('vendor_id', $vendorId)->where('document_type', $docType)->first();
        if ($existing) {
            Storage::disk('local')->delete("private/{$existing->file_path}");
            $existing->delete();
        }

        VendorDocument::create([
            'vendor_id' => $vendorId,
            'document_type' => $docType,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'uploaded_at' => now(),
            'status' => 'pending',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Document uploaded successfully.']);
        }

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Submit documents for admin review.
     */
    public function submitForReview(Request $request)
    {
        $vendor = $this->activeVendor($request);
        if (!$vendor) {
            return back()->with('error', 'Please create a business profile first.');
        }

        // Check required documents are uploaded
        $requiredTypes = ['govt_id', 'pan', 'aadhaar'];
        $uploaded = VendorDocument::where('vendor_id', (string) $vendor->getKey())->pluck('document_type')->all();
        $missing = array_diff($requiredTypes, $uploaded);

        if (!empty($missing)) {
            $labels = ['govt_id' => 'Government ID', 'pan' => 'PAN Card', 'aadhaar' => 'Aadhaar Card'];
            $missingLabels = array_map(fn($t) => $labels[$t] ?? $t, $missing);
            return back()->with('error', 'Please upload the following required documents: ' . implode(', ', $missingLabels));
        }

        $vendor->update(['verification_status' => 'under_review']);

        return back()->with('success', 'Your documents have been submitted for review. We will notify you once verified.');
    }

    /**
     * Resolve active vendor from session.
     */
    private function activeVendor(Request $request): ?Vendor
    {
        $user = $request->user();
        $activeId = $request->session()->get('active_business_id');

        if ($activeId) {
            $vendor = Vendor::where('_id', $activeId)->where('user_id', (string) $user->getKey())->first();
            if ($vendor) return $vendor;
        }

        return Vendor::where('user_id', (string) $user->getKey())->first();
    }
}
