<?php

namespace App\Services;

use App\Models\AktivitasPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PerawatanActivityRecorder
{
    public function complete(JadwalPerawatan $schedule, User $performer, ?string $note = null): bool
    {
        return DB::transaction(function () use ($schedule, $performer, $note): bool {
            $updated = JadwalPerawatan::query()
                ->whereKey($schedule->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);

            if ($updated === 0) {
                $currentStatus = JadwalPerawatan::query()
                    ->whereKey($schedule->id)
                    ->value('status');

                abort_if($currentStatus === null, 404);
                abort_unless($currentStatus === 'completed', 409, 'Jadwal yang terlewat tidak dapat ditandai selesai.');

                return false;
            }

            if (! AktivitasPerawatan::query()->where('jadwal_perawatan_id', $schedule->id)->exists()) {
                AktivitasPerawatan::create([
                    'tanaman_ditanam_id' => $schedule->tanaman_ditanam_id,
                    'jadwal_perawatan_id' => $schedule->id,
                    'performed_at' => now(),
                    'user_id' => $performer->id,
                    'catatan' => $note,
                ]);
            }

            return true;
        });
    }
}
