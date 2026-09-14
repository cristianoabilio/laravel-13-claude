<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'heading_prefix' => ['required', 'string', 'max:255'],
            'heading_highlight' => ['required', 'string', 'max:100'],
            'heading_suffix' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:5120'],
        ];
    }
}
