<?php

namespace App\Services\Patient;

use App\Models\MedicalRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MedicalRecordService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $patient, array $data, UploadedFile $file): MedicalRecord
    {
        $data['record_date'] = Carbon::createFromFormat('d/m/Y', $data['record_date']);
        $data['patient_id'] = $patient->id;

        $this->assignFile($data, $file);

        return MedicalRecord::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(MedicalRecord $medicalRecord, array $data, ?UploadedFile $file = null): MedicalRecord
    {
        $data['record_date'] = Carbon::createFromFormat('d/m/Y', $data['record_date']);

        if ($file) {
            $this->deleteFile($medicalRecord);
            $this->assignFile($data, $file);
        }

        $medicalRecord->update($data);

        return $medicalRecord->refresh();
    }

    public function delete(MedicalRecord $medicalRecord): void
    {
        $this->deleteFile($medicalRecord);

        $medicalRecord->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function assignFile(array &$data, UploadedFile $file): void
    {
        $data['file_path'] = $file->store('medical-records', 's3');
        $data['file_type'] = $file->getClientMimeType();
        $data['original_name'] = $file->getClientOriginalName();
    }

    protected function deleteFile(MedicalRecord $medicalRecord): void
    {
        if ($medicalRecord->file_path) {
            Storage::disk('s3')->delete($medicalRecord->file_path);
        }
    }
}
