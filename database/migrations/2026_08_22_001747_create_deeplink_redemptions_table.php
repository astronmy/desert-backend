<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deeplink_redemptions', function (Blueprint $table) {
            $table->id();
            $table->char('jti', 36);
            $table->string('device_id', 64);
            $table->string('feature', 32);
            $table->string('invitation_code', 32)->nullable();
            $table->timestamp('redeemed_at')->useCurrent();

            $table->unique(['jti', 'device_id'], 'uniq_jti_device');
            $table->index('jti', 'idx_jti');
            $table->index('invitation_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deeplink_redemptions');
    }
};
