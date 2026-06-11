<?php

use App\Models\CompanyProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('bisa membuat company profile lewat factory', function () {
    $profile = CompanyProfile::factory()->create();

    $this->assertDatabaseHas('company_profiles', [
        'id' => $profile->id,
        'name' => $profile->name,
    ]);
});

test('method get_profile mengembalikan satu data', function () {
    $this->seed(\Database\Seeders\CompanyProfileSeeder::class);

    $profile = CompanyProfile::getProfile();

    expect($profile)->toBeInstanceOf(CompanyProfile::class);
    expect($profile->name)->toBe('PT Travel Barokah Makmur');
});

test('accessor logo_full_url mengembalikan asset url', function () {
    $profile = CompanyProfile::factory()->create([
        'logo_url' => 'logos/company.png'
    ]);

    expect($profile->logo_full_url)->toContain('storage/logos/company.png');
});
