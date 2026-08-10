<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'first_name', 'last_name', 'email', 'password', 'profile_photo',
    'mobile', 'date_of_birth', 'address', 'city', 'state', 'zip_code', 'country', 'about',
])]
#[Hidden(['password', 'remember_token'])]
class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }
}
