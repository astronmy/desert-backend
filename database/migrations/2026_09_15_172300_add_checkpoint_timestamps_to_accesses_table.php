<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accesses', function (Blueprint $table) {
            $table->timestamp('entrada_at')->nullable()->after('guest_id_type');
            $table->timestamp('salon_at')->nullable()->after('entrada_at');
        });

        DB::table('accesses')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                DB::table('accesses')->where('id', $row->id)->update([
                    'entrada_at' => $row->accessed_at,
                    'salon_at' => null,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('accesses', function (Blueprint $table) {
            $table->dropColumn(['entrada_at', 'salon_at']);
        });
    }
};
