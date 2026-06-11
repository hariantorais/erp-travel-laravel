<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;
use App\Models\Branch;

class BranchForm extends Form
{
    // 1. Properti State Internal
    public ?int $branchId = null;
    public string $code = '';
    public string $name = '';
    public ?string $address = null;
    public ?string $phone = null;
    public ?string $leader_name = null;
    public bool $is_main_office = false;

    /**
     * Build Dynamic Validation Rules.
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:20|unique:branches,code,' . $this->branchId,
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'leader_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ];
    }

    /**
     * Hydrate data dari Eloquent Model ke Form State (Edit Mode).
     */
    public function setBranch(int $id): void
    {
        $branch = Branch::findOrFail($id);

        $this->branchId = $branch->id;
        $this->code = $branch->code;
        $this->name = $branch->name;
        $this->address = $branch->address;
        $this->phone = $branch->phone;
        $this->leader_name = $branch->leader_name;
        $this->is_main_office = (bool) $branch->is_main_office;
    }

    /**
     * Reset seluruh properti form menjadi nilai bawaan.
     */
    public function clear(): void
    {
        $this->reset(['branchId', 'code', 'name', 'address', 'phone', 'leader_name', 'is_main_office']);
    }
}