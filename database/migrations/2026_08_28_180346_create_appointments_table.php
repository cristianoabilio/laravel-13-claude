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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->nullable()->unique();

            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->foreignId('doctor_service_id')->nullable()->constrained('doctor_services')->nullOnDelete();

            // Snapshots of the service actually booked, so the appointment/invoice
            // stay accurate even if the doctor later edits or removes the service.
            $table->string('service_name')->nullable();
            $table->unsignedSmallInteger('duration_minutes');

            $table->enum('appointment_type', ['clinic', 'video_call', 'audio_call', 'chat', 'home_visit']);
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('home_visit_address')->nullable();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email');
            $table->string('symptoms')->nullable();
            $table->text('reason_for_visit')->nullable();
            $table->text('notes')->nullable();

            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('booking_fee', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            $table->timestamps();

            // Not a unique constraint: a cancelled appointment must free its slot
            // back up. Real double-booking prevention happens in the booking
            // service via a row lock inside the DB transaction; this index just
            // keeps the "is this slot taken" / dashboard lookups fast.
            $table->index(['doctor_id', 'appointment_date', 'start_time']);
            $table->index(['patient_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
