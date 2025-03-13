<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    

    $role = session('user_role', null);
    
    if(in_array($role, [0,1,2])){
      $verticalMenuJson = file_get_contents(base_path('resources/menu/proponent.json'));
    }else if($role == 3){
      $verticalMenuJson = file_get_contents(base_path('resources/menu/local_secretary.json'));
    }else if($role == 4){
      $verticalMenuJson = file_get_contents(base_path('resources/menu/university_secretary.json'));
    }else if($role == 5){
      $verticalMenuJson = file_get_contents(base_path('resources/menu/board_secretary.json'));
    }else if($role == 6){
      $verticalMenuJson = file_get_contents(base_path('resources/menu/non_council.json'));
    }else if($role == 7){
      $verticalMenuJson = file_get_contents(base_path('resources/menu/super_admin.json'));
    }
    
    $verticalMenuData = json_decode($verticalMenuJson);

    // Share all menuData to all the views
    View::share('menuData', $verticalMenuData);
  }
}
