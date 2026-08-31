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
        Schema::table('doctor_services', function (Blueprint $table) {
            // How long an appointment for this service blocks on the doctor's
            // calendar - drives slot generation for the booking wizard.
            $table->unsignedSmallInteger('duration_minutes')->default(30)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_services', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
        });
    }
};
