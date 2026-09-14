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
        Schema::create('home_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('icon');
            $table->string('icon_color');
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed with the original static template's items so the homepage
        // keeps showing the same content until an admin edits it.
        $defaults = [
            [
                'icon' => 'isax isax-tag-user5',
                'icon_color' => 'text-orange',
                'title' => 'Follow-Up Care',
                'description' => 'We ensure continuity of care through regular follow-ups and communication, helping you stay on track with health goals.',
            ],
            [
                'icon' => 'isax isax-voice-cricle',
                'icon_color' => 'text-purple',
                'title' => 'Patient-Centered Approach',
                'description' => 'We prioritize your comfort and preferences, tailoring our services to meet your individual needs and Care from Our Experts',
            ],
            [
                'icon' => 'isax isax-wallet-add-15',
                'icon_color' => 'text-cyan',
                'title' => 'Convenient Access',
                'description' => 'Easily book appointments online or through our dedicated customer service team, with flexible hours to fit your schedule.',
            ],
        ];

        $now = now();

        DB::table('home_reasons')->insert(array_map(
            fn (array $reason, int $index) => $reason + [
                'sort_order' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $defaults,
            array_keys($defaults),
        ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_reasons');
    }
};
