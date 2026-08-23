<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('scope');
            $table->string('status')->default('pending');
            $table->string('title');
            $table->text('message');
            $table->string('external_id')->nullable();
            $table->timestamp('send_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('event_notification_invitation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_notification_id')->constrained('event_notifications')->cascadeOnDelete();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_notification_id', 'invitation_id'], 'event_notification_invitation_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_notification_invitation');
        Schema::dropIfExists('event_notifications');
    }
};
