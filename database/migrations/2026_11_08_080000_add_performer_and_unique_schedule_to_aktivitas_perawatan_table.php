<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateScheduleIds = DB::table('aktivitas_perawatan')
            ->select('jadwal_perawatan_id')
            ->whereNotNull('jadwal_perawatan_id')
            ->groupBy('jadwal_perawatan_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('jadwal_perawatan_id');

        if ($duplicateScheduleIds->isNotEmpty()) {
            throw new \RuntimeException(
                'Cannot add the per-schedule activity constraint: duplicate activity records exist for schedule IDs '
                .$duplicateScheduleIds->implode(', ').'. Resolve these duplicates without deleting history, then retry.',
            );
        }

        Schema::table('aktivitas_perawatan', function (Blueprint $table): void {
            $table->foreignId('user_id')
                ->nullable()
                ->after('jadwal_perawatan_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->unique('jadwal_perawatan_id', 'aktivitas_perawatan_schedule_unique');
        });
    }

    public function down(): void
    {
        Schema::table('aktivitas_perawatan', function (Blueprint $table): void {
            $table->dropUnique('aktivitas_perawatan_schedule_unique');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
