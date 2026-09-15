<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('user_notifications', 'action_url')) {
            Schema::table('user_notifications', fn (Blueprint $table) => $table->string('action_url')->nullable()->after('message'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user_notifications', 'action_url')) {
            Schema::table('user_notifications', fn (Blueprint $table) => $table->dropColumn('action_url'));
        }
    }
};
