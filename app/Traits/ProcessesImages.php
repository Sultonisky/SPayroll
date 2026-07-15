<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

trait ProcessesImages
{
    private function processAndStoreImage($file, string $directory = 'uploads', int $maxWidth = 1000, int $quality = 80): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        // Cek apakah fungsi imagewebp() ada (GD mendukung WebP)
        if (function_exists('imagewebp')) {
            $extension = 'webp';
            $encoded = (string) $image->toWebp($quality);
        } else {
            // Fallback ke JPEG jika WebP tidak didukung
            $extension = 'jpg';
            $encoded = (string) $image->toJpeg($quality);
        }

        $filename = $directory . '/' . Str::random(40) . '.' . $extension;
        Storage::disk('supabase')->put($filename, $encoded);

        return $filename;
    }
}
