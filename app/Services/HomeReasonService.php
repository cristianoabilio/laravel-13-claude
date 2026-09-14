<?php

namespace App\Services;

use App\Models\HomeReason;
use App\Models\HomeReasonSection;
use Illuminate\Database\Eloquent\Collection;

class HomeReasonService
{
    /**
     * The single "Compelling Reasons to Choose" section header, with a
     * sensible default matching the original static template so the
     * homepage never renders blank text before an admin has saved anything.
     */
    public function section(): HomeReasonSection
    {
        return HomeReasonSection::first() ?? new HomeReasonSection([
            'badge_text' => 'Why Book With Us',
            'heading' => 'Compelling Reasons to Choose',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateSection(array $data): HomeReasonSection
    {
        $section = HomeReasonSection::first() ?? new HomeReasonSection();

        $section->fill($data);
        $section->save();

        return $section;
    }

    /**
     * @return Collection<int, HomeReason>
     */
    public function list(): Collection
    {
        return HomeReason::orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): HomeReason
    {
        $data['sort_order'] ??= ((int) HomeReason::max('sort_order')) + 1;

        return HomeReason::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(HomeReason $reason, array $data): HomeReason
    {
        $reason->update($data);

        return $reason;
    }

    public function delete(HomeReason $reason): void
    {
        $reason->delete();
    }
}
