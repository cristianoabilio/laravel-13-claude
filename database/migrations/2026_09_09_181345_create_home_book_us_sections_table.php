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
        Schema::create('home_book_us_sections', function (Blueprint $table) {
            $table->id();
            $table->string('badge_text')->nullable();
            $table->string('heading_prefix');
            $table->string('heading_highlight');
            $table->text('description');
            $table->string('image_one')->nullable();
            $table->string('image_two')->nullable();
            $table->string('image_three')->nullable();
            $table->timestamps();
        });

        DB::table('home_book_us_sections')->insert([
            'badge_text' => 'Why Book With Us',
            'heading_prefix' => 'We are committed to understanding your',
            'heading_highlight' => 'unique needs and delivering care.',
            'description' => 'As a trusted healthAs a trusted healthcare provider in our community, we are passionate about promoting health and wellness beyond the clinic. We actively engage in community outreach programs, health fairs, and educational workshop.',
            'image_one' => 'backend/assets/img/book-01.jpg',
            'image_two' => 'backend/assets/img/book-02.jpg',
            'image_three' => 'backend/assets/img/book-03.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_book_us_sections');
    }
};
