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
        Schema::create('home_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed with the original static template's items so the homepage
        // keeps showing the same content until an admin edits it - the
        // point of this table is to make already-existing copy editable,
        // not to start the section empty.
        $defaults = [
            'Multi Speciality Treatments & Doctors',
            'Lab Testing Services',
            'Medecines & Supplies',
            'Hospitals & Clinics',
            'Health Care Services',
            'Talk to Doctors',
            'Home Care Services',
        ];

        $now = now();

        DB::table('home_services')->insert(array_map(
            fn (string $title, int $index) => [
                'title' => $title,
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
        Schema::dropIfExists('home_services');
    }
};
