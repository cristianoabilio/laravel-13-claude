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
        Schema::create('home_reason_sections', function (Blueprint $table) {
            $table->id();
            $table->string('badge_text')->nullable();
            $table->string('heading');
            $table->timestamps();
        });

        // Singleton row seeded with the original static template's copy, so
        // the homepage keeps showing the same content until an admin edits it.
        DB::table('home_reason_sections')->insert([
            'badge_text' => 'Why Book With Us',
            'heading' => 'Compelling Reasons to Choose',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_reason_sections');
    }
};
