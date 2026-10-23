<?php

namespace App\Http\Controllers;

use App\Models\Penanam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenanamController extends Controller
{
    public function index(Request $request): View
    {
        $query = Penanam::query()->withCount('tanamanDitanam')->orderBy('nama');

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%'.$request->string('q')->toString().'%');
        }

        return view('penanam.index', ['penanams' => $query->paginate(12)->withQueryString()]);
    }
}
