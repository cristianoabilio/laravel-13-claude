<?php

namespace App\Services\Doctor;

use App\Models\User;
use App\Services\Doctor\Concerns\StoresSquareImages;
use Illuminate\Http\UploadedFile;

class DoctorProfileService
{
    use StoresSquareImages;

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
     * Update the doctor's password. The plain value is passed straight through -
     * the User model's "hashed" cast takes care of hashing it on save.
     */
    public function updatePassword(User $doctor, string $password): void
    {
        $doctor->update(['password' => $password]);
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

    protected function storePhoto(UploadedFile $photo): string
    {
        return $this->storeSquareImage($photo, 'doctors', self::PHOTO_SIZE);
    }

    protected function deletePhoto(User $doctor): void
    {
        $this->deleteStoredImage($doctor->profile_photo);
    }
}
