<?php

namespace App\Services\Doctor;

use App\Models\Education;
use App\Models\User;
use App\Services\Doctor\Concerns\StoresSquareImages;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class DoctorEducationService
{
    use StoresSquareImages;

    /**
     * Side length, in pixels, of the square institution logo thumbnail.
     */
    protected const LOGO_SIZE = 360;

    /**
     * Create or update each submitted education entry for the doctor.
     *
     * Rows with an "id" are updated (ownership already verified by the request's
     * validation rule); rows without one are created. Nothing is deleted here -
     * removal is a separate, explicit action per row.
     *
     * @param  array<int, array<string, mixed>>  $educations
     * @return Collection<int, Education>
     */
    public function upsert(User $doctor, array $educations): Collection
    {
        return collect($educations)->map(function (array $data) use ($doctor) {
            $id = $data['id'] ?? null;
            $logo = $data['logo'] ?? null;
            unset($data['id'], $data['logo']);

            $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date']);
            $data['end_date'] = Carbon::createFromFormat('d/m/Y', $data['end_date']);

            $education = $id
                ? $doctor->educations()->findOrFail($id)
                : $doctor->educations()->make();

            if ($logo instanceof UploadedFile) {
                $this->deleteStoredImage($education->logo);
                $data['logo'] = $this->storeSquareImage($logo, 'educations', self::LOGO_SIZE);
            }

            $education->fill($data);
            $education->doctor_id = $doctor->id;
            $education->save();

            return $education;
        });
    }

    /**
     * Delete an education entry and its logo.
     */
    public function delete(Education $education): void
    {
        $this->deleteStoredImage($education->logo);

        $education->delete();
    }

    /**
     * Remove an education entry's logo, keeping the rest of the record.
     */
    public function removeLogo(Education $education): Education
    {
        $this->deleteStoredImage($education->logo);

        $education->update(['logo' => null]);

        return $education->refresh();
    }
}
