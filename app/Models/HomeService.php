<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'url', 'sort_order'])]
class HomeService extends Model
{
    /** @use HasFactory<\Database\Factories\HomeServiceFactory> */
    use HasFactory;
}
