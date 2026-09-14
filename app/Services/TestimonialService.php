<?php

namespace App\Services;

use App\Models\Testimonial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use RuntimeException;

class TestimonialService
{
    /**
     * Width/height, in pixels, a testimonial's patient image is resized to before storage.
     */
    protected const IMAGE_WIDTH = 300;

    protected const IMAGE_HEIGHT = 300;

    public function list(): Collection
    {
        return Testimonial::orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $image = null): Testimonial
    {
        if ($image) {
            $data['image'] = $this->storeImage($image);
        }

        $data['sort_order'] ??= ((int) Testimonial::max('sort_order')) + 1;

        return Testimonial::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Testimonial $testimonial, array $data, ?UploadedFile $image = null): Testimonial
    {
        if ($image) {
            $this->deleteImage($testimonial);
            $data['image'] = $this->storeImage($image);
        }

        $testimonial->update($data);

        return $testimonial;
    }

    public function delete(Testimonial $testimonial): void
    {
        $this->deleteImage($testimonial);
        $testimonial->delete();
    }

    protected function storeImage(UploadedFile $image): string
    {
        $extension = strtolower($image->extension() ?: $image->getClientOriginalExtension() ?: 'jpg');

        // SVG is vector, not raster - GD can't decode/resize it (and doesn't
        // need to, since it scales losslessly at any display size), so it's
        // stored as-is instead of going through the cover-resize pipeline.
        if ($extension === 'svg') {
            $path = 'testimonials/'.Str::uuid().'.svg';
            $stored = Storage::disk('s3')->put($path, file_get_contents($image->getRealPath()), 'public');
        } else {
            $encoded = ImageManager::usingDriver(GdDriver::class)
                ->decodePath($image->getRealPath())
                ->cover(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
                ->encodeUsingFileExtension($extension, quality: 90);

            $path = 'testimonials/'.Str::uuid().'.'.$extension;
            $stored = Storage::disk('s3')->put($path, (string) $encoded, 'public');
        }

        if (! $stored) {
            throw new RuntimeException('Unable to upload the testimonial image to storage. Check the MinIO/S3 connection settings.');
        }

        return $path;
    }

    protected function deleteImage(Testimonial $testimonial): void
    {
        if ($testimonial->image && ! str_starts_with($testimonial->image, 'backend/')) {
            Storage::disk('s3')->delete($testimonial->image);
        }
    }
}
