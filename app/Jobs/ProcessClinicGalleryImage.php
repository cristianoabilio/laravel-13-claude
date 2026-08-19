<?php

namespace App\Jobs;

use App\Models\Clinic;
use App\Models\ClinicImage;
use App\Notifications\ClinicGalleryImageProcessed;
use App\Services\Concerns\StoresSquareImages;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessClinicGalleryImage implements ShouldQueue
{
    use Queueable, StoresSquareImages;

    /**
     * Side length, in pixels, of the square gallery thumbnail.
     */
    protected const IMAGE_SIZE = 300;

    /**
     * Number of times the job may be attempted before being given up on.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param  string  $tempPath  Path, on the s3 disk, of the raw upload staged during the
     *                            request - the original tmp upload no longer exists by the
     *                            time a queued job runs, so the request must persist it first.
     */
    public function __construct(
        public int $clinicId,
        public string $tempPath,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $clinic = Clinic::find($this->clinicId);

        if (! $clinic) {
            // The clinic was deleted before this job ran; discard the staged upload.
            Storage::disk('s3')->delete($this->tempPath);

            return;
        }

        $binary = Storage::disk('s3')->get($this->tempPath);

        if ($binary === null) {
            Log::warning('Clinic gallery upload could not be processed: staged file missing.', [
                'clinic_id' => $this->clinicId,
                'temp_path' => $this->tempPath,
            ]);

            return;
        }

        $extension = pathinfo($this->tempPath, PATHINFO_EXTENSION) ?: 'jpg';

        $path = $this->storeSquareImageFromBinary($binary, $extension, 'clinics/gallery', self::IMAGE_SIZE);

        $image = ClinicImage::create([
            'clinic_id' => $clinic->id,
            'image' => $path,
        ]);

        Storage::disk('s3')->delete($this->tempPath);

        $clinic->doctor->notify(new ClinicGalleryImageProcessed($image));
    }
}
