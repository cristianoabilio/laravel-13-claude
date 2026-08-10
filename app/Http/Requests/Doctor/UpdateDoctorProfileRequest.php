<?php

namespace App\Http\Requests\Doctor;

use App\Http\Requests\Doctor\Concerns\NormalizesKnownLanguages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
{
    use NormalizesKnownLanguages;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeKnownLanguages();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user()->id],
            'known_languages' => ['nullable', 'array'],
            'known_languages.*' => ['string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],

            'memberships' => ['nullable', 'array'],
            'memberships.*.title' => ['required_with:memberships.*.description', 'nullable', 'string', 'max:255'],
            'memberships.*.description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
