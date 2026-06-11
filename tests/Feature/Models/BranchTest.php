<?php

use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('bisa buat branch baru', function () {
    $branch = Branch::create([
        'code' => 'JKT',
        'name' => 'Jakarta Pusat',
        'address' => 'Jl. Sudirman',
        'phone' => '021-123456'
    ]);

    expect($branch->code)->toBe('JKT');
    $this->assertDatabaseHas('branches', ['code' => 'JKT']);
});

test('kode branch harus unique', function () {
    Branch::create(['code' => 'JKT', 'name' => 'Jakarta', 'address' => 'Jl', 'phone' => '021']);

    $this->expectException(\Illuminate\Database\QueryException::class);
    Branch::create(['code' => 'JKT', 'name' => 'Jakarta 2', 'address' => 'Jl', 'phone' => '021']);
});
