<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\CompanyProfile; // Pakai Model hanya untuk MediaLibrary

class CompanyProfileService
{
   public function getProfile(): ?object
   {
      return DB::table('company_profiles')->first();
   }

   public function updateProfile(array $data): void
   {
      DB::transaction(function () use ($data) {
         $payload = [
            'name' => $data['name'],
            'brand_name' => $data['brand_name'] ?? null,
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'siskopatuh_piu_code' => $data['siskopatuh_piu_code'],
            'siskopatuh_user' => $data['siskopatuh_user'] ?? null,
            'travel_license_no' => $data['travel_license_no'] ?? null,
            'updated_at' => now(),
         ];

         // Hanya encrypt jika user isi password baru
         if (!empty($data['siskopatuh_password'])) {
            $payload['siskopatuh_pass_encrypted'] = Crypt::encryptString($data['siskopatuh_password']);
         }

         $profile = DB::table('company_profiles')->first();

         if ($profile) {
            DB::table('company_profiles')->where('id', $profile->id)->update($payload);
         } else {
            $payload['created_at'] = now();
            DB::table('company_profiles')->insert($payload);
         }
      });
   }

   // Helper untuk MediaLibrary karena DB::table tidak bisa
   public function getProfileModel(): CompanyProfile
   {
      return CompanyProfile::firstOrCreate([]);
   }
}
