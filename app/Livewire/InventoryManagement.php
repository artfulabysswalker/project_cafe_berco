<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inventory;

class InventoryManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = 'all';
    public $selectedStatus = 'all';

    // Form fields for Add / Edit modal
    public $isModalOpen = false;
    public $isEditMode = false;
    public $inventoryId;
    public $item_code;
    public $item_name;
    public $category = 'Bahan Baku';
    public $current_stock = 0;
    public $unit = 'Pcs';
    public $min_stock = 0;

    protected $rules = [
        'item_code' => 'required|string|max:50',
        'item_name' => 'required|string|max:255',
        'category' => 'required|string|max:100',
        'current_stock' => 'required|numeric|min:0',
        'unit' => 'required|string|max:50',
        'min_stock' => 'required|numeric|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->reset(['inventoryId', 'item_code', 'item_name', 'category', 'current_stock', 'unit', 'min_stock', 'isEditMode']);
        $this->category = 'Bahan Baku';
        $this->unit = 'Pcs';
        $this->isModalOpen = true;
    }

    public function openEditModal($id)
    {
        $item = Inventory::findOrFail($id);
        $this->inventoryId = $item->id;
        $this->item_code = $item->item_code;
        $this->item_name = $item->item_name;
        $this->category = $item->category;
        $this->current_stock = $item->current_stock;
        $this->unit = $item->unit;
        $this->min_stock = $item->min_stock;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function saveItem()
    {
        $this->validate();

        if ($this->isEditMode && $this->inventoryId) {
            $item = Inventory::findOrFail($this->inventoryId);
            $item->update([
                'item_code' => $this->item_code,
                'item_name' => $this->item_name,
                'category' => $this->category,
                'current_stock' => $this->current_stock,
                'unit' => $this->unit,
                'min_stock' => $this->min_stock,
            ]);
            session()->flash('message', 'Item ' . $this->item_name . ' berhasil diperbarui!');
        } else {
            Inventory::create([
                'item_code' => $this->item_code,
                'item_name' => $this->item_name,
                'category' => $this->category,
                'current_stock' => $this->current_stock,
                'unit' => $this->unit,
                'min_stock' => $this->min_stock,
            ]);
            session()->flash('message', 'Item ' . $this->item_name . ' berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function adjustStock($id, $amount)
    {
        $item = Inventory::findOrFail($id);
        $newStock = max(0, $item->current_stock + $amount);
        $item->update(['current_stock' => $newStock]);
        session()->flash('message', 'Stok ' . $item->item_name . ' diperbarui: ' . number_format($newStock) . ' ' . $item->unit);
    }

    public function deleteItem($id)
    {
        $item = Inventory::findOrFail($id);
        $itemName = $item->item_name;
        $item->delete();
        session()->flash('message', 'Item ' . $itemName . ' berhasil dihapus dari stok!');
    }

    public function render()
    {
        $query = Inventory::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('item_code', 'like', '%' . $this->search . '%')
                  ->orWhere('item_name', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedCategory !== 'all') {
            $query->where('category', $this->selectedCategory);
        }

        if ($this->selectedStatus !== 'all') {
            $query->where('status', $this->selectedStatus);
        }

        $items = $query->orderBy('status', 'desc')->orderBy('item_name', 'asc')->paginate(10);
        $categories = Inventory::select('category')->distinct()->pluck('category');

        // Summary KPI Counts
        $totalItems = Inventory::count();
        $safeCount = Inventory::where('status', 'aman')->count();
        $warningCount = Inventory::where('status', 'menipis')->count();
        $criticalCount = Inventory::where('status', 'kritis')->count();

        return view('livewire.inventory-management', compact(
            'items', 'categories', 'totalItems', 'safeCount', 'warningCount', 'criticalCount'
        ));
    }
}
