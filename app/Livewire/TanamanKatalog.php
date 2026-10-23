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

    public bool $featured = false;

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
            ->when($this->search !== '', fn ($query) => $query->where('nama', 'like', '%'.$this->search.'%'))
            ->when($this->kategori !== '', fn ($query) => $query->where('kategori', $this->kategori))
            ->orderBy('nama');

        return view('livewire.tanaman-katalog', [
            'tanamans' => $this->featured ? $query->limit(3)->get() : $query->paginate(9),
            'kategoriList' => Tanaman::query()->where('is_active', true)->distinct()->orderBy('kategori')->pluck('kategori'),
            'featured' => $this->featured,
        ]);
    }
}
