<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TanamanResource;
use App\Models\Tanaman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TanamanApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $tanamans = Tanaman::query()
            ->where('is_active', true)
            ->when($request->filled('q'), fn ($query) => $query->where('nama', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('kategori'), fn ($query) => $query->where('kategori', $request->string('kategori')))
            ->orderBy('nama')
            ->get();

        return TanamanResource::collection($tanamans);
    }

    public function show(Tanaman $tanaman): TanamanResource
    {
        abort_unless($tanaman->is_active, 404);

        return new TanamanResource($tanaman->load([
            'aturanPerawatan' => fn ($query) => $query->where('is_active', true),
        ]));
    }
}
