<?php

namespace App\Services;

use App\Models\Faq;
use Illuminate\Support\Collection;

class FaqService
{
    public function list(): Collection
    {
        return Faq::orderBy('sort_order')->orderBy('id')->get();
    }

    public function create(array $data): Faq
    {
        $data['sort_order'] ??= ((int) Faq::max('sort_order')) + 1;

        return Faq::create($data);
    }

    public function update(Faq $faq, array $data): Faq
    {
        $faq->update($data);

        return $faq;
    }

    public function delete(Faq $faq): void
    {
        $faq->delete();
    }
}
