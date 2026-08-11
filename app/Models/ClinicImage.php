<?php

namespace App\Models;

use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['clinic_id', 'image'])]
class ClinicImage extends Model
{
    /** @use HasFactory<\Database\Factories\ClinicImageFactory> */
    use HasFactory, ResolvesStorageUrl;

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * The publicly resolvable URL for the gallery image, whether it's a
     * bundled template asset (public/backend/...) or an upload on the S3/MinIO disk.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->image),
        );
    }
}
