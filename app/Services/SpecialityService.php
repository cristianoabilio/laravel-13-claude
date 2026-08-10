<?php

namespace App\Services;

use App\Models\Speciality;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SpecialityService
{
    /**
     * Paginated list of specialities for the admin index page.
     */
    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return Speciality::query()->latest()->paginate($perPage);
    }

    public function create(array $data, ?UploadedFile $image = null): Speciality
    {
        if ($image) {
            $data['image'] = $this->storeImage($image);
        }

        return Speciality::create($data);
    }

    public function update(Speciality $speciality, array $data, ?UploadedFile $image = null): Speciality
    {
        if ($image) {
            $this->deleteImage($speciality);
            $data['image'] = $this->storeImage($image);
        }

        $speciality->update($data);

        return $speciality;
    }

    public function delete(Speciality $speciality): void
    {
        $this->deleteImage($speciality);
        $speciality->delete();
    }

    protected function storeImage(UploadedFile $image): string
    {
        $path = $image->store('specialities', ['disk' => 's3', 'visibility' => 'public']);

        if (! $path) {
            throw new RuntimeException('Unable to upload the speciality image to storage. Check the MinIO/S3 connection settings.');
        }

        return $path;
    }

    protected function deleteImage(Speciality $speciality): void
    {
        if ($speciality->image && ! str_starts_with($speciality->image, 'admin/')) {
            Storage::disk('s3')->delete($speciality->image);
        }
    }
}
