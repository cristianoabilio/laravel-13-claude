<?php

namespace App\Services\Doctor;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use RuntimeException;

class DoctorProfileService
{
    /**
     * Side length, in pixels, of the square profile photo thumbnail.
     */
    protected const PHOTO_SIZE = 360;

    /**
     * Update the doctor's profile information, avatar, and memberships.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(User $doctor, array $data, ?UploadedFile $photo = null): User
    {
        $memberships = $data['memberships'] ?? [];
        unset($data['memberships']);

        if ($photo) {
            $this->deletePhoto($doctor);
            $data['profile_photo'] = $this->storePhoto($photo);
        }

        $doctor->update($data);

        $this->syncMemberships($doctor, $memberships);

        return $doctor->refresh();
    }

    /**
     * Remove the doctor's current profile photo, if any.
     */
    public function removePhoto(User $doctor): User
    {
        $this->deletePhoto($doctor);

        $doctor->update(['profile_photo' => null]);

        return $doctor->refresh();
    }

    /**
     * Update the doctor's known languages independently of the rest of the profile.
     *
     * @param  array<int, string>  $languages
     */
    public function updateLanguages(User $doctor, array $languages): User
    {
        $doctor->update(['known_languages' => $languages]);

        return $doctor->refresh();
    }

    /**
     * Replace the doctor's memberships with the submitted list, dropping blank rows.
     *
     * @param  array<int, array{title?: string|null, description?: string|null}>  $memberships
     */
    protected function syncMemberships(User $doctor, array $memberships): void
    {
        // Force-delete: this replaces the full list on every save, so soft-deleting
        // here would just pile up trashed rows instead of recording a real removal.
        $doctor->memberships()->forceDelete();

        collect($memberships)
            ->filter(fn (array $membership) => filled($membership['title'] ?? null))
            ->each(fn (array $membership) => $doctor->memberships()->create([
                'title' => $membership['title'],
                'description' => $membership['description'] ?? null,
            ]));
    }

    /**
     * Resize the upload to a {@see self::PHOTO_SIZE} square, preserving aspect ratio via a
     * center crop (no stretching), and store it on S3/MinIO.
     */
    protected function storePhoto(UploadedFile $photo): string
    {
        $extension = $photo->extension() ?: 'jpg';

        $encoded = ImageManager::usingDriver(GdDriver::class)
            ->decodePath($photo->getRealPath())
            ->cover(self::PHOTO_SIZE, self::PHOTO_SIZE)
            ->encodeUsingFileExtension($extension, quality: 90);

        $path = 'doctors/'.Str::uuid().'.'.$extension;

        $stored = Storage::disk('s3')->put($path, (string) $encoded, 'public');

        if (! $stored) {
            throw new RuntimeException('Unable to upload the profile photo to storage. Check the MinIO/S3 connection settings.');
        }

        return $path;
    }

    protected function deletePhoto(User $doctor): void
    {
        if ($doctor->profile_photo && ! str_starts_with($doctor->profile_photo, 'backend/')) {
            Storage::disk('s3')->delete($doctor->profile_photo);
        }
    }
}
