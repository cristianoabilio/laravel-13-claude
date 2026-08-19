<?php

namespace App\Services\Patient;

use App\Models\User;
use App\Services\Concerns\StoresSquareImages;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class PatientProfileService
{
    use StoresSquareImages;

    /**
     * Side length, in pixels, of the square profile photo thumbnail.
     */
    protected const PHOTO_SIZE = 360;

    /**
     * Update the patient's profile information and avatar.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(User $patient, array $data, ?UploadedFile $photo = null): User
    {
        if (! empty($data['date_of_birth'])) {
            $data['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $data['date_of_birth']);
        }

        if ($photo) {
            $this->deletePhoto($patient);
            $data['profile_photo'] = $this->storePhoto($photo);
        }

        $patient->update($data);

        return $patient->refresh();
    }

    /**
     * Remove the patient's current profile photo, if any.
     */
    public function removePhoto(User $patient): User
    {
        $this->deletePhoto($patient);

        $patient->update(['profile_photo' => null]);

        return $patient->refresh();
    }

    /**
     * Update the patient's password. The plain value is passed straight through -
     * the User model's "hashed" cast takes care of hashing it on save.
     */
    public function updatePassword(User $patient, string $password): void
    {
        $patient->update(['password' => $password]);
    }

    protected function storePhoto(UploadedFile $photo): string
    {
        return $this->storeSquareImage($photo, 'patients', self::PHOTO_SIZE);
    }

    protected function deletePhoto(User $patient): void
    {
        $this->deleteStoredImage($patient->profile_photo);
    }
}
