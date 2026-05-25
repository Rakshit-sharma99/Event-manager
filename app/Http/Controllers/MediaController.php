<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Vendor;
use App\Models\VendorGalleryImage;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected $uploadService;

    public function __construct(MediaUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Upload User Profile Photo.
     */
    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $file = $request->file('profile_photo');

        $path = $this->uploadService->uploadProfilePhoto($file, $user->profile_photo);
        $user->update(['profile_photo' => $path]);

        // Support AJAX responses for live updating avatar previews
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully.',
                'path' => $path,
                'url' => asset('storage/' . $path),
            ]);
        }

        return back()->with('success', 'Profile picture updated successfully.');
    }

    /**
     * Remove User Profile Photo.
     */
    public function deleteProfilePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo) {
            $this->uploadService->deleteMedia($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile picture removed successfully.',
                'url' => $user->avatar_url, // fallback initials avatar URL
            ]);
        }

        return back()->with('success', 'Profile picture removed.');
    }

    /**
     * Upload Vendor Portfolio Showcase Image.
     */
    public function uploadVendorGallery(Request $request)
    {
        $user = $request->user();
        $vendor = Vendor::where('user_id', (string) $user->getKey())->first();

        if (!$vendor) {
            return response()->json(['error' => 'Vendor profile not found.'], 404);
        }

        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $uploaded = [];
        $files = $request->file('images');
        
        // Find current max sort order to append cleanly
        $maxSort = VendorGalleryImage::where('vendor_id', (string) $vendor->getKey())->max('sort_order') ?? 0;

        foreach ($files as $index => $file) {
            $path = $this->uploadService->uploadVendorGallery($file);
            
            $galleryImg = VendorGalleryImage::create([
                'vendor_id' => (string) $vendor->getKey(),
                'image_path' => $path,
                'sort_order' => $maxSort + $index + 1,
            ]);

            $uploaded[] = [
                'id' => (string) $galleryImg->getKey(),
                'path' => $path,
                'url' => asset('storage/' . $path),
            ];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($uploaded) . ' image(s) added to gallery.',
                'images' => $uploaded,
            ]);
        }

        return back()->with('success', count($uploaded) . ' image(s) added to gallery.');
    }

    /**
     * Delete Vendor Portfolio Showcase Image.
     */
    public function deleteVendorGallery(Request $request, string $id)
    {
        $user = $request->user();
        $vendor = Vendor::where('user_id', (string) $user->getKey())->first();

        if (!$vendor) {
            return response()->json(['error' => 'Vendor profile not found.'], 404);
        }

        $galleryImg = VendorGalleryImage::where('_id', $id)
            ->where('vendor_id', (string) $vendor->getKey())
            ->first();

        if (!$galleryImg) {
            return response()->json(['error' => 'Gallery image not found or unauthorized.'], 403);
        }

        // Delete from storage disk
        $this->uploadService->deleteMedia($galleryImg->image_path);

        // Delete from database
        $galleryImg->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Showcase image deleted successfully.',
            ]);
        }

        return back()->with('success', 'Showcase image deleted.');
    }

    /**
     * Upload Event Cover Image.
     */
    public function uploadEventCover(Request $request, string $eventId)
    {
        $user = $request->user();
        $event = Event::where('_id', $eventId)
            ->where('user_id', (string) $user->getKey())
            ->first();

        if (!$event) {
            return response()->json(['error' => 'Event not found or unauthorized.'], 403);
        }

        $request->validate([
            'cover_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $file = $request->file('cover_image');

        $path = $this->uploadService->uploadEventCover($file, $event->cover_image);
        $event->update(['cover_image' => $path]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Event cover updated successfully.',
                'path' => $path,
                'url' => asset('storage/' . $path),
            ]);
        }

        return back()->with('success', 'Event cover updated.');
    }
}
