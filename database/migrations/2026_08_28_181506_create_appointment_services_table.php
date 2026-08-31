<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointment_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_service_id')->nullable()->constrained('doctor_services')->nullOnDelete();

            // Snapshots at booking time, same reasoning as appointments.service_name:
            // the invoice must stay accurate even if the doctor edits the service later.
            $table->string('service_name');
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('duration_minutes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_services');
    }
};
