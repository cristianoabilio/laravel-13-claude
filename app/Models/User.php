<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\ResolvesStorageUrl;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'role', 'first_name', 'last_name', 'email', 'phone', 'password', 'profile_photo',
    'date_of_birth', 'gender', 'blood_group', 'address', 'city', 'state', 'country', 'pincode',
    'display_name', 'designation', 'known_languages', 'availability_status',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, ResolvesStorageUrl, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deleted_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'known_languages' => 'array',
        ];
    }

    /**
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'doctor_id');
    }

    /**
     * @return HasMany<Experience, $this>
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'doctor_id')->orderByDesc('start_date');
    }

    /**
     * @return HasMany<Education, $this>
     */
    public function educations(): HasMany
    {
        return $this->hasMany(Education::class, 'doctor_id')->orderByDesc('start_date');
    }

    /**
     * @return HasMany<Clinic, $this>
     */
    public function clinics(): HasMany
    {
        return $this->hasMany(Clinic::class, 'doctor_id');
    }

    /**
     * The publicly resolvable URL for the profile photo, whether it's a
     * bundled template asset (public/backend/...) or an upload on the S3/MinIO disk.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->profile_photo),
        );
    }
}
