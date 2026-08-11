<?php

namespace App\Services\Doctor;

use App\Models\Experience;
use App\Models\User;
use App\Services\Doctor\Concerns\StoresSquareImages;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class DoctorExperienceService
{
    use StoresSquareImages;

    /**
     * Side length, in pixels, of the square hospital logo thumbnail.
     */
    protected const LOGO_SIZE = 360;

    /**
     * Create or update each submitted experience for the doctor.
     *
     * Rows with an "id" are updated (ownership already verified by the request's
     * validation rule); rows without one are created. Nothing is deleted here -
     * removal is a separate, explicit action per row.
     *
     * @param  array<int, array<string, mixed>>  $experiences
     * @return Collection<int, Experience>
     */
    public function upsert(User $doctor, array $experiences): Collection
    {
        return collect($experiences)->map(function (array $data) use ($doctor) {
            $id = $data['id'] ?? null;
            $logo = $data['hospital_logo'] ?? null;
            unset($data['id'], $data['hospital_logo']);

            $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date']);
            $data['currently_working'] = (bool) ($data['currently_working'] ?? false);
            $data['end_date'] = $data['currently_working']
                ? null
                : Carbon::createFromFormat('d/m/Y', $data['end_date']);

            $experience = $id
                ? $doctor->experiences()->findOrFail($id)
                : $doctor->experiences()->make();

            if ($logo instanceof UploadedFile) {
                $this->deleteStoredImage($experience->hospital_logo);
                $data['hospital_logo'] = $this->storeSquareImage($logo, 'experiences', self::LOGO_SIZE);
            }

            $experience->fill($data);
            $experience->doctor_id = $doctor->id;
            $experience->save();

            return $experience;
        });
    }

    /**
     * Delete an experience and its hospital logo.
     */
    public function delete(Experience $experience): void
    {
        $this->deleteStoredImage($experience->hospital_logo);

        $experience->delete();
    }

    /**
     * Remove an experience's hospital logo, keeping the rest of the record.
     */
    public function removeLogo(Experience $experience): Experience
    {
        $this->deleteStoredImage($experience->hospital_logo);

        $experience->update(['hospital_logo' => null]);

        return $experience->refresh();
    }
}
