<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registration_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('short_code', 16)->unique();
            $table->text('token');
            $table->char('jti', 36);
            $table->timestamp('expires_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'revoked_at']);
        });

        Schema::create('registration_link_hits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_link_id')
                ->constrained('event_registration_links')
                ->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->timestamp('visited_at');
            $table->string('ip_hash', 64);
            $table->string('user_agent', 512)->nullable();
            $table->string('device_type', 16)->default('unknown');
            $table->string('os', 32)->nullable();
            $table->string('browser', 32)->nullable();
            $table->string('referrer', 512)->nullable();
            $table->boolean('is_store_click')->default(false);
            $table->string('store', 16)->nullable();

            $table->index(['event_id', 'visited_at'], 'rlh_event_visited_idx');
            $table->index(['event_registration_link_id', 'visited_at'], 'rlh_link_visited_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_link_hits');
        Schema::dropIfExists('event_registration_links');
    }
};
