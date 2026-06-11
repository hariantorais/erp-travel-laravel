<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleRedirectService
{
   /**
    * Menentukan rute tujuan berdasarkan Role pengguna yang terautentikasi.
    */
   public function getRedirectRoute(): string
   {
      $user = Auth::user();

      if (!$user instanceof User) {
         return '/login';
      }

      // Mapping Role Spatie ke URI rute dashboard masing-masing
      return match (true) {
         $user->hasRole('super_admin')      => '/admin/dashboard',
         $user->hasRole('branch_manager')   => '/manager/dashboard',
         $user->hasRole('finance')          => '/finance/dashboard',
         $user->hasRole('document_staff')   => '/document/dashboard',
         $user->hasRole('sales_agent')      => '/sales/dashboard',
         default                            => '/',
      };
   }
}
