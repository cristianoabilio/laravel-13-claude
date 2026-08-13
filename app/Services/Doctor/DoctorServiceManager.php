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
     * @param  array<int, array{service_id: int, price: float|string, description?: string|null}>  $services
     * @return Collection<int, DoctorService>
     */
    public function upsert(User $doctor, array $services): Collection
    {
        return collect($services)->map(fn (array $data) => DoctorService::updateOrCreate(
            ['doctor_id' => $doctor->id, 'service_id' => $data['service_id']],
            ['price' => $data['price'], 'description' => $data['description'] ?? null],
        ));
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
