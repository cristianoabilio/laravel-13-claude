<?php

namespace App\Models;

use App\Enums\PayoutRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'doctor_id', 'amount', 'description', 'status',
    'bank_name', 'branch_name', 'account_number', 'account_holder_name',
    'processed_at', 'processed_by',
])]
class PayoutRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PayoutRequestFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PayoutRequestStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    /**
     * The snapshotted account number with everything but the last 4 digits
     * masked, for safe on-screen display.
     */
    protected function maskedAccountNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->account_number ? '••••'.substr($this->account_number, -4) : null,
        );
    }
}
