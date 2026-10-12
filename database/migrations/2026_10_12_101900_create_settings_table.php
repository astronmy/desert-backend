<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('texto_registro')->default(true);
            $table->boolean('texto_confirmacion')->default(true);
            $table->boolean('texto_recordatorio')->default(true);
            $table->timestamps();
        });

        DB::table('settings')->insert([
            'texto_registro' => true,
            'texto_confirmacion' => true,
            'texto_recordatorio' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
