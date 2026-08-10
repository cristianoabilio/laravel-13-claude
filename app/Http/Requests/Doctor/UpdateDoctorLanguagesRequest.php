<?php

namespace App\Http\Requests\Doctor;

use App\Http\Requests\Doctor\Concerns\NormalizesKnownLanguages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorLanguagesRequest extends FormRequest
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
            'known_languages' => ['nullable', 'array'],
            'known_languages.*' => ['string', 'max:255'],
        ];
    }
}
