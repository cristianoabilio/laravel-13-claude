<?php

namespace App\Models;

use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['patient_id', 'title', 'record_for', 'record_date', 'comments', 'file_path', 'file_type', 'original_name'])]
class MedicalRecord extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalRecordFactory> */
    use HasFactory, ResolvesStorageUrl;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'record_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * The publicly resolvable URL for the uploaded file.
     */
    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->file_path),
        );
    }
}
