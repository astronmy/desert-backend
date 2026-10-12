<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('mensaje_registro')->nullable()->after('texto_registro');
            $table->text('mensaje_confirmacion')->nullable()->after('texto_confirmacion');
            $table->text('mensaje_recordatorio')->nullable()->after('texto_recordatorio');
        });

        DB::table('settings')->update([
            'mensaje_registro' => Setting::DEFAULT_MENSAJE_REGISTRO,
            'mensaje_confirmacion' => Setting::DEFAULT_MENSAJE_CONFIRMACION,
        ]);
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'mensaje_registro',
                'mensaje_confirmacion',
                'mensaje_recordatorio',
            ]);
        });
    }
};
