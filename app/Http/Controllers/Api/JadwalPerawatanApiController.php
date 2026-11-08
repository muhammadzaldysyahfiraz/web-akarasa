<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalPerawatanResource;
use App\Models\JadwalPerawatan;
use App\Models\User;
use App\Services\PerawatanActivityRecorder;
use Illuminate\Http\Request;

class JadwalPerawatanApiController extends Controller
{
    public function complete(
        Request $request,
        JadwalPerawatan $perawatan,
        PerawatanActivityRecorder $activities,
    ): JadwalPerawatanResource
    {
        $data = $request->validate([
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $activities->complete($perawatan, $user, $data['catatan'] ?? null);

        return new JadwalPerawatanResource($perawatan->fresh()->load('aturanPerawatan', 'aktivitasPerawatan.user'));
    }
}
