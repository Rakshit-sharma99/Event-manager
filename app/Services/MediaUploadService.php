<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    /**
     * Upload and process profile photo.
     */
    public function uploadProfilePhoto(UploadedFile $file, ?string $oldPath = null): string
    {
        if ($oldPath) {
            $this->deleteMedia($oldPath);
        }

        return $this->processAndStore($file, 'profile-photos', 300, 300);
    }

    /**
     * Upload and process vendor gallery showcase image.
     */
    public function uploadVendorGallery(UploadedFile $file): string
    {
        return $this->processAndStore($file, 'vendor-gallery', 1200, 800, false);
    }

    /**
     * Upload and process event cover banner.
     */
    public function uploadEventCover(UploadedFile $file, ?string $oldPath = null): string
    {
        if ($oldPath) {
            $this->deleteMedia($oldPath);
        }

        return $this->processAndStore($file, 'event-covers', 1200, 500, false);
    }

    /**
     * Safely delete media file from storage disk.
     */
    public function deleteMedia(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        // Avoid deleting external Unsplash seeded URLs or absolute paths
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return false;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Process image (resize/compress/webp conversion if GD/Imagick active) and store on public disk.
     */
    private function processAndStore(
        UploadedFile $file,
        string $folder,
        int $width,
        int $height,
        bool $crop = true
    ): string {
        $extension = 'webp';
        $filename = Str::random(40) . '.' . $extension;
        $targetPath = $folder . '/' . $filename;

        // Check if image manipulation is possible (Intervention + GD/Imagick extension)
        $hasGd = extension_loaded('gd');
        $hasImagick = extension_loaded('imagick');
        $hasIntervention = class_exists(\Intervention\Image\ImageManager::class);

        if (($hasGd || $hasImagick) && $hasIntervention) {
            try {
                // Initialize Intervention ImageManager
                $manager = class_exists(\Intervention\Image\ImageManager::class)
                    ? new \Intervention\Image\ImageManager(['driver' => $hasGd ? 'gd' : 'imagick'])
                    : null;

                if ($manager) {
                    $img = $manager->make($file->getRealPath());

                    // Resize or crop
                    if ($crop) {
                        $img->fit($width, $height, function ($constraint) {
                            $constraint->upsize();
                        });
                    } else {
                        $img->resize($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    // Encode as WebP with 80% compression quality
                    $encoded = $img->encode('webp', 80);

                    // Store on public storage disk
                    Storage::disk('public')->put($targetPath, (string) $encoded);

                    return $targetPath;
                }
            } catch (\Throwable $e) {
                // Log or fail gracefully to fallback copy
                logger()->warning('Image manipulation failed, falling back to direct copy: ' . $e->getMessage());
            }
        }

        // --- Fallback Mechanism ---
        // If GD/Imagick or Intervention is not available/functional, store the original image
        $fallbackExt = $file->getClientOriginalExtension();
        $fallbackFilename = Str::random(40) . '.' . ($fallbackExt ?: 'jpg');
        $fallbackPath = $folder . '/' . $fallbackFilename;

        Storage::disk('public')->putFileAs($folder, $file, $fallbackFilename);

        return $fallbackPath;
    }
}
