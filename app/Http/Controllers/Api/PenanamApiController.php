<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PenanamResource;
use App\Models\Penanam;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PenanamApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PenanamResource::collection(
            Penanam::query()
                ->when($request->filled('q'), fn ($query) => $query->where('nama', 'like', '%'.$request->string('q').'%'))
                ->orderBy('nama')
                ->get(),
        );
    }
}
