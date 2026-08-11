<?php

use App\Jobs\ProcessClinicGalleryImage;
use App\Models\Clinic;
use App\Models\ClinicImage;
use App\Notifications\ClinicGalleryImageProcessed;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
    Notification::fake();
});

test('it resizes the staged upload to a 300x300 square, creates a gallery image, and notifies the doctor', function () {
    $clinic = Clinic::factory()->create();

    $raw = imagecreatetruecolor(1200, 800);
    imagefill($raw, 0, 0, imagecolorallocate($raw, 100, 150, 200));
    ob_start();
    imagejpeg($raw);
    $binary = ob_get_clean();

    $tempPath = 'clinic-uploads/tmp/test-image.jpg';
    Storage::disk('s3')->put($tempPath, $binary);

    (new ProcessClinicGalleryImage($clinic->id, $tempPath))->handle();

    expect(ClinicImage::count())->toBe(1);

    $image = ClinicImage::first();
    expect($image->clinic_id)->toBe($clinic->id);
    Storage::disk('s3')->assertExists($image->image);
    Storage::disk('s3')->assertMissing($tempPath);

    [$width, $height] = getimagesize(Storage::disk('s3')->path($image->image));
    expect($width)->toBe(300);
    expect($height)->toBe(300);

    Notification::assertSentTo(
        $clinic->doctor,
        ClinicGalleryImageProcessed::class,
        fn (ClinicGalleryImageProcessed $notification) => $notification->clinicImage->is($image)
    );
});

test('it discards the staged upload if the clinic was deleted before the job ran', function () {
    $tempPath = 'clinic-uploads/tmp/orphaned.jpg';
    Storage::disk('s3')->put($tempPath, 'fake-image-bytes');

    (new ProcessClinicGalleryImage(0, $tempPath))->handle();

    expect(ClinicImage::count())->toBe(0);
    Storage::disk('s3')->assertMissing($tempPath);
    Notification::assertNothingSent();
});

test('it does nothing if the staged upload is missing', function () {
    $clinic = Clinic::factory()->create();

    (new ProcessClinicGalleryImage($clinic->id, 'clinic-uploads/tmp/does-not-exist.jpg'))->handle();

    expect(ClinicImage::count())->toBe(0);
    Notification::assertNothingSent();
});
