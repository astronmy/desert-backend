<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('microservice_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->string('method', 10);
            $table->string('endpoint');
            $table->longText('request_body')->nullable();
            $table->longText('response_body')->nullable();
            $table->unsignedSmallInteger('response_code');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['service', 'created_at']);
            $table->index('response_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('microservice_logs');
    }
};
