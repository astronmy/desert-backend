<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedInteger('invitation_limit')->default(0)->after('host');
        });

        $counts = DB::table('invitations')
            ->select('event_id', DB::raw('COUNT(*) as confirmed_count'))
            ->where('status', 'confirmed')
            ->groupBy('event_id')
            ->pluck('confirmed_count', 'event_id');

        foreach ($counts as $eventId => $count) {
            DB::table('events')->where('id', $eventId)->update([
                'invitation_limit' => (int) $count,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('invitation_limit');
        });
    }
};
