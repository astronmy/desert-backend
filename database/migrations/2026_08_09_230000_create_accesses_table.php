<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('invitation_code');
            $table->string('guest_first_name');
            $table->string('guest_last_name');
            $table->string('guest_document_number');
            $table->string('guest_id_type');
            $table->timestamp('accessed_at');
            $table->timestamps();

            $table->unique('invitation_id');
            $table->index(['event_id', 'accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accesses');
    }
};
