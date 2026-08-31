<?php

namespace App\Models;

use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['appointment_id', 'file_path', 'file_type', 'original_name'])]
class AppointmentDocument extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentDocumentFactory> */
    use HasFactory, ResolvesStorageUrl;

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * The publicly resolvable URL for the uploaded document.
     */
    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->file_path),
        );
    }
}
