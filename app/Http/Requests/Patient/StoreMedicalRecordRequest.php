<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'record_for' => ['required', 'string', 'max:255'],
            'record_date' => ['required', 'date_format:d/m/Y'],
            'comments' => ['required', 'string'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
