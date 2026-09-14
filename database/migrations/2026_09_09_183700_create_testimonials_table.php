<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('quote');
            $table->string('patient_name');
            $table->string('patient_country');
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $defaults = [
            [
                'title' => 'Nice Treatment',
                'quote' => 'I had a wonderful experience the staff was friendly and attentive, and Dr. Smith took the time to explain everything clearly.',
                'patient_name' => 'Deny Hendrawan',
                'patient_country' => 'United States',
                'image' => 'backend/assets/img/patients/patient22.jpg',
            ],
            [
                'title' => 'Good Hospitability',
                'quote' => 'Genuinely cares about his patients. He helped me understand my condition and worked with me to create a plan.',
                'patient_name' => 'Johnson DWayne',
                'patient_country' => 'United States',
                'image' => 'backend/assets/img/patients/patient21.jpg',
            ],
            [
                'title' => 'Nice Treatment',
                'quote' => 'I had a great experience with Dr. Chen. She was not only professional but also made me feel comfortable discussing.',
                'patient_name' => 'Rayan Smith',
                'patient_country' => 'United States',
                'image' => 'backend/assets/img/patients/patient.jpg',
            ],
            [
                'title' => 'Excellent Service',
                'quote' => 'I had a wonderful experience the staff was friendly and attentive, and Dr. Smith took the time to explain everything clearly.',
                'patient_name' => 'Sofia Doe',
                'patient_country' => 'United States',
                'image' => 'backend/assets/img/patients/patient23.jpg',
            ],
        ];

        foreach ($defaults as $index => $testimonial) {
            DB::table('testimonials')->insert([
                ...$testimonial,
                'rating' => 5,
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
