<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_reports', function (Blueprint $table): void {
            $table->decimal('reward_amount', 12, 2)->default(0)->after('phone');
        });

        Schema::table('document_claims', function (Blueprint $table): void {
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('payment_proof_path')->nullable()->after('payment_reference');
            $table->decimal('paid_amount', 12, 2)->default(0)->after('payment_proof_path');
            $table->decimal('service_fee_amount', 12, 2)->default(0)->after('paid_amount');
            $table->timestamp('payment_submitted_at')->nullable()->after('service_fee_amount');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_submitted_at');
            $table->foreignId('payment_verified_by')->nullable()->after('payment_verified_at')->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable()->after('payment_verified_by');
            $table->foreignId('completed_by')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('document_claims', function (Blueprint $table): void {
            $table->dropForeign(['payment_verified_by']);
            $table->dropForeign(['completed_by']);
            $table->dropColumn([
                'payment_method',
                'payment_reference',
                'payment_proof_path',
                'paid_amount',
                'service_fee_amount',
                'payment_submitted_at',
                'payment_verified_at',
                'payment_verified_by',
                'completed_at',
                'completed_by',
            ]);
        });

        Schema::table('document_reports', function (Blueprint $table): void {
            $table->dropColumn('reward_amount');
        });
    }
};
