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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $defaults = [
            [
                'question' => 'How do I book an appointment with a doctor?',
                'answer' => 'Yes, simply visit our website and log in or create an account. Search for a doctor based on specialization, location, or availability & confirm your booking.',
            ],
            [
                'question' => 'Can I request a specific doctor when booking my appointment?',
                'answer' => 'Yes, you can usually request a specific doctor when booking your appointment, though availability may vary based on their schedule.',
            ],
            [
                'question' => 'What should I do if I need to cancel or reschedule my appointment?',
                'answer' => 'If you need to cancel or reschedule your appointment, contact the doctor as soon as possible to inform them and to reschedule for another available time slot.',
            ],
            [
                'question' => "What if I'm running late for my appointment?",
                'answer' => "If you know you will be late, it's courteous to call the doctor's office and inform them. Depending on their policy and schedule, they may be able to accommodate you or reschedule your appointment.",
            ],
            [
                'question' => 'Can I book appointments for family members or dependents?',
                'answer' => 'Yes, in many cases, you can book appointments for family members or dependents. However, you may need to provide their personal information and consent to do so.',
            ],
        ];

        foreach ($defaults as $index => $faq) {
            DB::table('faqs')->insert([
                ...$faq,
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
        Schema::dropIfExists('faqs');
    }
};
