<?php

namespace App\Http\Controllers;

use App\Models\Tanaman;
use Illuminate\View\View;

class TanamanController extends Controller
{
    public function index(): View
    {
        return view('tanaman.index');
    }

    public function show(Tanaman $tanaman): View
    {
        abort_unless($tanaman->is_active, 404);

        return view('tanaman.show', [
            'tanaman' => $tanaman->load([
                'aturanPerawatan' => fn ($query) => $query->where('is_active', true),
            ]),
        ]);
    }
}
