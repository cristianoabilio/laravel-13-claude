<?php

namespace App\Services;

use App\Models\HomeBanner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use RuntimeException;

class HomeBannerService
{
    /**
     * Width, in pixels, the banner image is resized to before storage.
     */
    protected const IMAGE_WIDTH = 464;

    /**
     * Height, in pixels, the banner image is resized to before storage.
     */
    protected const IMAGE_HEIGHT = 606;

    /**
     * The single home banner row, with sensible defaults matching the
     * original static template so the homepage never renders blank text
     * before an admin has saved anything.
     */
    public function current(): HomeBanner
    {
        return HomeBanner::first() ?? new HomeBanner([
            'heading_prefix' => 'Discover Health: Find Your Trusted',
            'heading_highlight' => 'Doctors',
            'heading_suffix' => 'Today',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(array $data, ?UploadedFile $image = null): HomeBanner
    {
        $banner = HomeBanner::first() ?? new HomeBanner();

        if ($image) {
            $this->deleteImage($banner);
            $data['image'] = $this->storeImage($image);
        }

        $banner->fill($data);
        $banner->save();

        return $banner;
    }

    protected function storeImage(UploadedFile $image): string
    {
        $extension = strtolower($image->extension() ?: $image->getClientOriginalExtension() ?: 'jpg');

        // SVG is vector, not raster - GD can't decode/resize it (and doesn't
        // need to, since it scales losslessly at any display size), so it's
        // stored as-is instead of going through the cover-resize pipeline.
        if ($extension === 'svg') {
            $path = 'home-banner/'.Str::uuid().'.svg';
            $stored = Storage::disk('s3')->put($path, file_get_contents($image->getRealPath()), 'public');
        } else {
            $encoded = ImageManager::usingDriver(GdDriver::class)
                ->decodePath($image->getRealPath())
                ->cover(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
                ->encodeUsingFileExtension($extension, quality: 90);

            $path = 'home-banner/'.Str::uuid().'.'.$extension;
            $stored = Storage::disk('s3')->put($path, (string) $encoded, 'public');
        }

        if (! $stored) {
            throw new RuntimeException('Unable to upload the banner image to storage. Check the MinIO/S3 connection settings.');
        }

        return $path;
    }

    protected function deleteImage(HomeBanner $banner): void
    {
        if ($banner->image && ! str_starts_with($banner->image, 'backend/')) {
            Storage::disk('s3')->delete($banner->image);
        }
    }
}
