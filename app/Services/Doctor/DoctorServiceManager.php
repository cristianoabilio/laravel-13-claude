<?php

namespace App\Services\Doctor;

use App\Models\DoctorService;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Support\Collection;

class DoctorServiceManager
{
    /**
     * The doctor's own service offerings, grouped by speciality, for display.
     *
     * @return Collection<int, array{speciality: Speciality, services: Collection<int, DoctorService>}>
     */
    public function forDoctor(User $doctor): Collection
    {
        return $doctor->doctorServices()
            ->with('service.speciality')
            ->get()
            ->groupBy(fn (DoctorService $doctorService) => $doctorService->service->speciality_id)
            ->map(fn (Collection $services) => [
                'speciality' => $services->first()->service->speciality,
                'services' => $services,
            ])
            ->sortBy(fn (array $group) => $group['speciality']->name)
            ->values();
    }

    /**
     * Create or update each submitted service offering for the doctor. Nothing is
     * deleted here - removal is a separate, explicit action per row/speciality.
     *
     * @param  array<int, array{service_id: int, price: float|string, duration_minutes?: int|string|null, description?: string|null}>  $services
     * @return Collection<int, DoctorService>
     */
    public function upsert(User $doctor, array $services): Collection
    {
        return collect($services)->map(function (array $data) use ($doctor) {
            $doctorService = DoctorService::firstOrNew(['doctor_id' => $doctor->id, 'service_id' => $data['service_id']]);

            $doctorService->price = $data['price'];
            $doctorService->duration_minutes = $data['duration_minutes'] ?? $doctorService->duration_minutes ?: 30;
            $doctorService->description = $data['description'] ?? null;
            $doctorService->save();

            return $doctorService;
        });
    }

    /**
     * Remove a single service offering.
     */
    public function delete(DoctorService $doctorService): void
    {
        $doctorService->delete();
    }

    /**
     * Remove every offering the doctor has under a given speciality.
     */
    public function deleteForSpeciality(User $doctor, Speciality $speciality): void
    {
        $doctor->doctorServices()
            ->whereHas('service', fn ($query) => $query->where('speciality_id', $speciality->id))
            ->delete();
    }
}
