<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->decimal('wallet_balance', 12, 2)->default(0)->after('avatar_path');
        });

        Schema::table('document_claims', function (Blueprint $table): void {
            $table->string('payout_status')->default('pending')->after('completed_by');
            $table->decimal('payout_amount', 12, 2)->default(0)->after('payout_status');
            $table->timestamp('paid_out_at')->nullable()->after('payout_amount');
        });
    }

    public function down(): void
    {
        Schema::table('document_claims', function (Blueprint $table): void {
            $table->dropColumn(['payout_status', 'payout_amount', 'paid_out_at']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('wallet_balance');
        });
    }
};
