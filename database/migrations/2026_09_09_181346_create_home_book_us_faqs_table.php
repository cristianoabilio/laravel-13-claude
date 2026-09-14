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
        Schema::create('home_book_us_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $description = 'We envision a community where everyone has access to high-quality healthcare and the resources they need to lead healthy, fulfilling lives.';

        $defaults = [
            ['title' => 'Our Vision', 'description' => $description],
            ['title' => 'Our Mission', 'description' => $description],
        ];

        foreach ($defaults as $index => $faq) {
            DB::table('home_book_us_faqs')->insert([
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
        Schema::dropIfExists('home_book_us_faqs');
    }
};
