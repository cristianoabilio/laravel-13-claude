<?php

namespace App\Models;

use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['doctor_id', 'logo', 'name', 'location', 'address'])]
class Clinic extends Model
{
    /** @use HasFactory<\Database\Factories\ClinicFactory> */
    use HasFactory, ResolvesStorageUrl;

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * @return HasMany<ClinicImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ClinicImage::class);
    }

    /**
     * The publicly resolvable URL for the clinic logo, whether it's a
     * bundled template asset (public/backend/...) or an upload on the S3/MinIO disk.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->logo),
        );
    }
}
