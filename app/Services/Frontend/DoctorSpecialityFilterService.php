<?php

namespace App\Services\Frontend;

use App\Models\Speciality;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class DoctorSpecialityFilterService
{
    /**
     * Doctors offering at least one service within this speciality, narrowed
     * by whatever filters are present on the request's query string. Every
     * filter is applied as an indexed WHERE/EXISTS clause so this stays a
     * single query (plus eager loads) regardless of catalog size.
     */
    public function search(Speciality $speciality, Request $request): LengthAwarePaginator
    {
        $search = trim((string) $request->query('search', ''));
        $serviceIds = $this->intArray($request->query('services', []));
        $genders = array_values(array_intersect((array) $request->query('gender', []), ['male', 'female']));
        $availableOnly = $request->boolean('available');
        $minPrice = $request->filled('min_price') ? (float) $request->query('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->query('max_price') : null;
        $experienceThresholds = $this->intArray($request->query('experience', []));

        return User::query()
            ->where('role', 'doctor')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('display_name', 'like', "%{$search}%");
                });
            })
            ->when($genders !== [], fn ($query) => $query->whereIn('gender', $genders))
            ->when($availableOnly, fn ($query) => $query->where('availability_status', 'available'))
            ->when($experienceThresholds !== [], function ($query) use ($experienceThresholds) {
                $query->where(function ($q) use ($experienceThresholds) {
                    foreach ($experienceThresholds as $years) {
                        $q->orWhereHas('experiences', fn ($e) => $e->where('years_of_experience', '>=', $years));
                    }
                });
            })
            ->whereHas('doctorServices', function ($query) use ($speciality, $serviceIds, $minPrice, $maxPrice) {
                $query->whereHas('service', fn ($q) => $q->where('speciality_id', $speciality->id));

                if ($serviceIds !== []) {
                    $query->whereIn('service_id', $serviceIds);
                }

                if ($minPrice !== null) {
                    $query->where('price', '>=', $minPrice);
                }

                if ($maxPrice !== null) {
                    $query->where('price', '<=', $maxPrice);
                }
            })
            ->with([
                'clinics' => fn ($q) => $q->latest()->limit(1),
                'doctorServices' => fn ($q) => $q->whereHas('service', fn ($q2) => $q2->where('speciality_id', $speciality->id))
                    ->orderBy('price')
                    ->limit(1),
            ])
            ->orderByRaw("availability_status = 'available' desc")
            ->orderBy('first_name')
            ->paginate(9)
            ->withQueryString();
    }

    /**
     * @return array<int, int>
     */
    protected function intArray(mixed $value): array
    {
        return array_values(array_unique(array_filter(array_map('intval', (array) $value))));
    }
}
