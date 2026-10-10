<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_control_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('response_code')->constrained()->nullOnDelete();
            $table->foreignId('event_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('access_control_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_id');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
