<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Analytics extends Controller
{
  public function index()
  {
    $role = session('user_role');  
    $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0)); 

    // dd($role);
    if(in_array($role, [0,1,2,6])){

    }else if(in_array($role, [3, 4])){

    }else if ($role == 5){

    }


    return view('content.dashboard.dashboards-analytics');
  }
}
