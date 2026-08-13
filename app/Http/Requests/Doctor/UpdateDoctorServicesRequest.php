<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorServicesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Drop stub rows added via "Add New Service" that were never given a service.
     */
    protected function prepareForValidation(): void
    {
        $services = collect($this->input('services', []))
            ->filter(fn (array $service) => filled($service['service_id'] ?? null))
            ->values()
            ->all();

        $this->merge(['services' => $services]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'services' => ['array'],
            'services.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'services.*.price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'services.*.description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
