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
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('status')->default('pending');

            // Bank details are snapshotted at request time so a later edit to the
            // doctor's bank account never changes what an already-submitted (or
            // already-processed) request says it was paid out to.
            $table->string('bank_name');
            $table->string('branch_name')->nullable();
            $table->string('account_number');
            $table->string('account_holder_name');

            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['doctor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
    }
};
