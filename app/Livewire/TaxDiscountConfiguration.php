<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TaxConfiguration;
use App\Models\DiscountScheme;
use Illuminate\Support\Facades\Auth;

class TaxDiscountConfiguration extends Component
{
    public $mode = 'view'; // view, edit_tax, edit_discount
    
    // Tax Configuration
    public $taxConfigurations = [];
    public $activeTaxConfig = null;
    public $taxName = '';
    public $taxPercentage = '';
    public $taxDescription = '';
    public $taxId = null;
    
    // Discount Scheme
    public $discountSchemes = [];
    public $discountCode = '';
    public $discountName = '';
    public $discountType = 'percentage';
    public $discountValue = '';
    public $discountMinPurchase = '';
    public $discountMaxDiscount = '';
    public $discountMaxUses = '';
    public $discountId = null;

    public function mount()
    {
        $this->loadTaxConfigurations();
        $this->loadDiscountSchemes();
    }

    public function loadTaxConfigurations()
    {
        $this->taxConfigurations = TaxConfiguration::where('id_user', Auth::id())
            ->latest('created_at')
            ->get()
            ->map(fn ($tax) => [
                'id' => $tax->id_tax_config,
                'name' => $tax->name,
                'percentage' => $tax->tax_percentage,
                'description' => $tax->description,
                'is_active' => $tax->is_active,
                'effective_from' => $tax->effective_from?->format('Y-m-d H:i'),
                'effective_until' => $tax->effective_until?->format('Y-m-d H:i'),
            ])
            ->toArray();

        $this->activeTaxConfig = TaxConfiguration::getActiveConfiguration();
    }

    public function loadDiscountSchemes()
    {
        $this->discountSchemes = DiscountScheme::where('id_user', Auth::id())
            ->latest('created_at')
            ->get()
            ->map(fn ($discount) => [
                'id' => $discount->id_discount_scheme,
                'code' => $discount->code,
                'name' => $discount->name,
                'type' => $discount->discount_type,
                'value' => $discount->discount_value,
                'min_purchase' => $discount->min_purchase,
                'max_discount' => $discount->max_discount,
                'max_uses' => $discount->max_uses,
                'is_active' => $discount->is_active,
                'valid_from' => $discount->valid_from?->format('Y-m-d H:i'),
                'valid_until' => $discount->valid_until?->format('Y-m-d H:i'),
            ])
            ->toArray();
    }

    // TAX CONFIGURATION
    public function editTax($taxId)
    {
        $tax = TaxConfiguration::find($taxId);
        if ($tax) {
            $this->taxId = $tax->id_tax_config;
            $this->taxName = $tax->name;
            $this->taxPercentage = $tax->tax_percentage;
            $this->taxDescription = $tax->description;
            $this->mode = 'edit_tax';
        }
    }

    public function saveTax()
    {
        $this->validate([
            'taxName' => 'required|string',
            'taxPercentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($this->taxId) {
            $tax = TaxConfiguration::find($this->taxId);
            $tax->update([
                'name' => $this->taxName,
                'tax_percentage' => $this->taxPercentage,
                'description' => $this->taxDescription,
            ]);
        } else {
            TaxConfiguration::create([
                'name' => $this->taxName,
                'tax_percentage' => $this->taxPercentage,
                'description' => $this->taxDescription,
                'is_active' => false,
                'id_user' => Auth::id(),
            ]);
        }

        $this->resetTaxFields();
        $this->loadTaxConfigurations();
        $this->dispatch('success', 'Konfigurasi pajak berhasil disimpan!');
    }

    public function activateTax($taxId)
    {
        TaxConfiguration::where('id_user', Auth::id())->update(['is_active' => false]);
        TaxConfiguration::find($taxId)->update(['is_active' => true]);
        
        $this->loadTaxConfigurations();
        $this->dispatch('success', 'Pajak diaktifkan!');
    }

    public function deleteTax($taxId)
    {
        TaxConfiguration::find($taxId)->delete();
        $this->loadTaxConfigurations();
        $this->dispatch('success', 'Konfigurasi pajak dihapus!');
    }

    public function resetTaxFields()
    {
        $this->taxId = null;
        $this->taxName = '';
        $this->taxPercentage = '';
        $this->taxDescription = '';
        $this->mode = 'view';
    }

    // DISCOUNT SCHEME
    public function editDiscount($discountId)
    {
        $discount = DiscountScheme::find($discountId);
        if ($discount) {
            $this->discountId = $discount->id_discount_scheme;
            $this->discountCode = $discount->code;
            $this->discountName = $discount->name;
            $this->discountType = $discount->discount_type;
            $this->discountValue = $discount->discount_value;
            $this->discountMinPurchase = $discount->min_purchase;
            $this->discountMaxDiscount = $discount->max_discount;
            $this->discountMaxUses = $discount->max_uses;
            $this->mode = 'edit_discount';
        }
    }

    public function saveDiscount()
    {
        $this->validate([
            'discountCode' => 'required|string',
            'discountName' => 'required|string',
            'discountType' => 'required|in:percentage,fixed',
            'discountValue' => 'required|numeric|min:0',
        ]);

        if ($this->discountId) {
            $discount = DiscountScheme::find($this->discountId);
            $discount->update([
                'code' => $this->discountCode,
                'name' => $this->discountName,
                'discount_type' => $this->discountType,
                'discount_value' => $this->discountValue,
                'min_purchase' => $this->discountMinPurchase,
                'max_discount' => $this->discountMaxDiscount,
                'max_uses' => $this->discountMaxUses,
            ]);
        } else {
            DiscountScheme::create([
                'code' => $this->discountCode,
                'name' => $this->discountName,
                'discount_type' => $this->discountType,
                'discount_value' => $this->discountValue,
                'min_purchase' => $this->discountMinPurchase,
                'max_discount' => $this->discountMaxDiscount,
                'max_uses' => $this->discountMaxUses,
                'is_active' => false,
                'valid_from' => now(),
                'valid_until' => now()->addMonth(),
                'id_user' => Auth::id(),
            ]);
        }

        $this->resetDiscountFields();
        $this->loadDiscountSchemes();
        $this->dispatch('success', 'Skema diskon berhasil disimpan!');
    }

    public function activateDiscount($discountId)
    {
        DiscountScheme::find($discountId)->update(['is_active' => true]);
        $this->loadDiscountSchemes();
        $this->dispatch('success', 'Diskon diaktifkan!');
    }

    public function deactivateDiscount($discountId)
    {
        DiscountScheme::find($discountId)->update(['is_active' => false]);
        $this->loadDiscountSchemes();
        $this->dispatch('success', 'Diskon dinonaktifkan!');
    }

    public function deleteDiscount($discountId)
    {
        DiscountScheme::find($discountId)->delete();
        $this->loadDiscountSchemes();
        $this->dispatch('success', 'Skema diskon dihapus!');
    }

    public function resetDiscountFields()
    {
        $this->discountId = null;
        $this->discountCode = '';
        $this->discountName = '';
        $this->discountType = 'percentage';
        $this->discountValue = '';
        $this->discountMinPurchase = '';
        $this->discountMaxDiscount = '';
        $this->discountMaxUses = '';
        $this->mode = 'view';
    }

    public function render()
    {
        return view('livewire.tax-discount-configuration', [
            'taxConfigurations' => $this->taxConfigurations,
            'activeTaxConfig' => $this->activeTaxConfig,
            'discountSchemes' => $this->discountSchemes,
            'mode' => $this->mode,
        ]);
    }
}
