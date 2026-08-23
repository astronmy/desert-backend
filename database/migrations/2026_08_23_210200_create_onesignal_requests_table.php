<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onesignal_requests', function (Blueprint $table) {
            $table->id();
            $table->string('method');
            $table->string('uri');
            $table->json('body')->nullable();
            $table->json('response')->nullable();
            $table->unsignedSmallInteger('status');
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onesignal_requests');
    }
};
