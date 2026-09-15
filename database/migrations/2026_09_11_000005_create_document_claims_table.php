<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('claimant_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->dateTime('appointment_at')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->timestamps();
            $table->unique(['document_report_id', 'claimant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_claims');
    }
};
