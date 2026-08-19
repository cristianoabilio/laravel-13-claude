<?php

namespace App\Services\Doctor;

use App\Jobs\ProcessClinicGalleryImage;
use App\Models\Clinic;
use App\Models\ClinicImage;
use App\Models\User;
use App\Services\Concerns\StoresSquareImages;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorClinicService
{
    use StoresSquareImages;

    /**
     * Side length, in pixels, of the square clinic logo thumbnail.
     */
    protected const LOGO_SIZE = 360;

    /**
     * Create or update each submitted clinic for the doctor.
     *
     * Rows with an "id" are updated (ownership already verified by the request's
     * validation rule); rows without one are created. Logos are resized and stored
     * synchronously; gallery images are staged and handed off to a queued job so a
     * clinic with several new photos doesn't block the request. Nothing is deleted
     * here - removal is a separate, explicit action per row/image.
     *
     * @param  array<int, array<string, mixed>>  $clinics
     * @return Collection<int, Clinic>
     */
    public function upsert(User $doctor, array $clinics): Collection
    {
        return collect($clinics)->map(function (array $data) use ($doctor) {
            $id = $data['id'] ?? null;
            $logo = $data['logo'] ?? null;
            $gallery = $data['gallery'] ?? [];
            unset($data['id'], $data['logo'], $data['gallery']);

            $clinic = $id
                ? $doctor->clinics()->findOrFail($id)
                : $doctor->clinics()->make();

            if ($logo instanceof UploadedFile) {
                $this->deleteStoredImage($clinic->logo);
                $data['logo'] = $this->storeSquareImage($logo, 'clinics', self::LOGO_SIZE);
            }

            $clinic->fill($data);
            $clinic->doctor_id = $doctor->id;
            $clinic->save();

            $this->queueGalleryUploads($clinic, $gallery);

            return $clinic;
        });
    }

    /**
     * Delete a clinic, its logo, and every gallery image (files and rows).
     */
    public function delete(Clinic $clinic): void
    {
        $this->deleteStoredImage($clinic->logo);

        foreach ($clinic->images as $image) {
            $this->deleteStoredImage($image->image);
        }

        $clinic->delete();
    }

    /**
     * Remove a clinic's logo, keeping the rest of the record.
     */
    public function removeLogo(Clinic $clinic): Clinic
    {
        $this->deleteStoredImage($clinic->logo);

        $clinic->update(['logo' => null]);

        return $clinic->refresh();
    }

    /**
     * Remove a single gallery image.
     */
    public function removeGalleryImage(ClinicImage $image): void
    {
        $this->deleteStoredImage($image->image);

        $image->delete();
    }

    /**
     * Stage each new gallery upload on S3 and queue it for async resizing/processing.
     *
     * @param  array<int, UploadedFile>  $gallery
     */
    protected function queueGalleryUploads(Clinic $clinic, array $gallery): void
    {
        foreach ($gallery as $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $extension = $image->extension() ?: 'jpg';
            $tempPath = 'clinic-uploads/tmp/'.Str::uuid().'.'.$extension;

            Storage::disk('s3')->put($tempPath, file_get_contents($image->getRealPath()));

            ProcessClinicGalleryImage::dispatch($clinic->id, $tempPath);
        }
    }
}
