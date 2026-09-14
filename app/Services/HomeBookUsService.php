<?php

namespace App\Services;

use App\Models\HomeBookUsFaq;
use App\Models\HomeBookUsSection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use RuntimeException;

class HomeBookUsService
{
    /**
     * Target width/height, in pixels, each image field is resized to before storage.
     */
    protected const IMAGE_DIMENSIONS = [
        'image_one' => [1060, 516],
        'image_two' => [512, 516],
        'image_three' => [512, 516],
    ];

    /**
     * The single book-us section row, with sensible defaults matching the
     * original static template so the homepage never renders blank before
     * an admin has saved anything.
     */
    public function section(): HomeBookUsSection
    {
        return HomeBookUsSection::first() ?? new HomeBookUsSection([
            'badge_text' => 'Why Book With Us',
            'heading_prefix' => 'We are committed to understanding your',
            'heading_highlight' => 'unique needs and delivering care.',
            'description' => 'As a trusted healthAs a trusted healthcare provider in our community, we are passionate about promoting health and wellness beyond the clinic. We actively engage in community outreach programs, health fairs, and educational workshop.',
            'image_one' => 'backend/assets/img/book-01.jpg',
            'image_two' => 'backend/assets/img/book-02.jpg',
            'image_three' => 'backend/assets/img/book-03.jpg',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, ?UploadedFile>  $images  keyed by image_one/image_two/image_three
     */
    public function updateSection(array $data, array $images): HomeBookUsSection
    {
        $section = HomeBookUsSection::first() ?? new HomeBookUsSection();

        foreach ($images as $field => $image) {
            if ($image) {
                $this->deleteImage($section, $field);
                $data[$field] = $this->storeImage($image, $field);
            }
        }

        $section->fill($data);
        $section->save();

        return $section;
    }

    protected function storeImage(UploadedFile $image, string $field): string
    {
        [$width, $height] = self::IMAGE_DIMENSIONS[$field];
        $extension = strtolower($image->extension() ?: $image->getClientOriginalExtension() ?: 'jpg');

        // SVG is vector, not raster - GD can't decode/resize it (and doesn't
        // need to, since it scales losslessly at any display size), so it's
        // stored as-is instead of going through the cover-resize pipeline.
        if ($extension === 'svg') {
            $path = 'home-bookus/'.Str::uuid().'.svg';
            $stored = Storage::disk('s3')->put($path, file_get_contents($image->getRealPath()), 'public');
        } else {
            $encoded = ImageManager::usingDriver(GdDriver::class)
                ->decodePath($image->getRealPath())
                ->cover($width, $height)
                ->encodeUsingFileExtension($extension, quality: 90);

            $path = 'home-bookus/'.Str::uuid().'.'.$extension;
            $stored = Storage::disk('s3')->put($path, (string) $encoded, 'public');
        }

        if (! $stored) {
            throw new RuntimeException('Unable to upload the image to storage. Check the MinIO/S3 connection settings.');
        }

        return $path;
    }

    protected function deleteImage(HomeBookUsSection $section, string $field): void
    {
        $current = $section->{$field};

        if ($current && ! str_starts_with($current, 'backend/')) {
            Storage::disk('s3')->delete($current);
        }
    }

    public function faqs(): Collection
    {
        return HomeBookUsFaq::orderBy('sort_order')->orderBy('id')->get();
    }

    public function createFaq(array $data): HomeBookUsFaq
    {
        $data['sort_order'] ??= ((int) HomeBookUsFaq::max('sort_order')) + 1;

        return HomeBookUsFaq::create($data);
    }

    public function updateFaq(HomeBookUsFaq $faq, array $data): HomeBookUsFaq
    {
        $faq->update($data);

        return $faq;
    }

    public function deleteFaq(HomeBookUsFaq $faq): void
    {
        $faq->delete();
    }
}
