<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_id', 'bank_name', 'branch_name', 'account_number', 'account_holder_name'])]
class DoctorBankAccount extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorBankAccountFactory> */
    use HasFactory;

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * The account number with everything but the last 4 digits masked, for
     * safe on-screen display.
     */
    protected function maskedAccountNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->account_number ? '••••'.substr($this->account_number, -4) : null,
        );
    }
}
