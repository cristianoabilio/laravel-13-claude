<?php

namespace App\Services\Doctor;

use App\Models\DoctorBankAccount;
use App\Models\User;

class DoctorBankAccountService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function save(User $doctor, array $data): DoctorBankAccount
    {
        return DoctorBankAccount::updateOrCreate(
            ['doctor_id' => $doctor->id],
            $data,
        );
    }
}
