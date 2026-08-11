<?php

namespace App\Services\Doctor\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use RuntimeException;

trait StoresSquareImages
{
    /**
     * Resize the upload to a square (center-cropped, no stretching) and store it on S3/MinIO.
     */
    protected function storeSquareImage(UploadedFile $image, string $directory, int $size = 360, int $quality = 90): string
    {
        $extension = $image->extension() ?: 'jpg';

        $encoded = ImageManager::usingDriver(GdDriver::class)
            ->decodePath($image->getRealPath())
            ->cover($size, $size)
            ->encodeUsingFileExtension($extension, quality: $quality);

        $path = $directory.'/'.Str::uuid().'.'.$extension;

        $stored = Storage::disk('s3')->put($path, (string) $encoded, 'public');

        if (! $stored) {
            throw new RuntimeException('Unable to upload the image to storage. Check the MinIO/S3 connection settings.');
        }

        return $path;
    }

    /**
     * Delete a previously stored image, skipping bundled template assets.
     */
    protected function deleteStoredImage(?string $path, string $bundledPrefix = 'backend/'): void
    {
        if ($path && ! str_starts_with($path, $bundledPrefix)) {
            Storage::disk('s3')->delete($path);
        }
    }
}
