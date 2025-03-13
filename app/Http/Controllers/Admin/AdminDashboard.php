<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboard extends Controller
{
    public function index()
    {
      $role = session('user_role');  
  
  
      return view('content.Admin.dashboard');
    }}
