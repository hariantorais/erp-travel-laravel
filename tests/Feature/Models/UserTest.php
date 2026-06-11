<?php

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user punya relasi ke cabang', function () {
    $branch = Branch::factory()->create(['name' => 'Pekanbaru']);
    $user = User::factory()->create(['branch_id' => $branch->id]);

    expect($user->branch)->toBeInstanceOf(Branch::class);
    expect($user->branch->name)->toBe('Pekanbaru');
});
