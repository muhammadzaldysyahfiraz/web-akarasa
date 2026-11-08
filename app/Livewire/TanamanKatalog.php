<?php

namespace App\Livewire;

use App\Models\Tanaman;
use Livewire\Component;
use Livewire\WithPagination;

class TanamanKatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $kategori = '';

    public function mount(): void
    {
        $this->search = (string) request()->query('search', '');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKategori(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Tanaman::query()
            ->where('is_active', true)
            ->where('nama', '!=', 'Jahe')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_ilmiah', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->kategori !== '', function ($query) {
                $query->where('kategori', $this->kategori);
            })
            ->orderBy('nama');

        return view('livewire.tanaman-katalog', [
            'tanamans' => $query->paginate(15),
            'kategoriList' => Tanaman::where('is_active', true)
                ->where('nama', '!=', 'Jahe')
                ->select('kategori')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori'),
        ]);
    }
}