<?php

namespace App\Services;

use App\Models\HomeService;
use Illuminate\Database\Eloquent\Collection;

class HomeServiceManager
{
    /**
     * @return Collection<int, HomeService>
     */
    public function list(): Collection
    {
        return HomeService::orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): HomeService
    {
        $data['sort_order'] ??= ((int) HomeService::max('sort_order')) + 1;

        return HomeService::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(HomeService $homeService, array $data): HomeService
    {
        $homeService->update($data);

        return $homeService;
    }

    public function delete(HomeService $homeService): void
    {
        $homeService->delete();
    }
}
