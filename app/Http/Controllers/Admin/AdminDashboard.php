<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Controller
{
    public function index()
    {
      $role = session('user_role');  
      $totalUsers = User::count();

      // Get employee_ids for each membership
      $academicIds = DB::table('academic_council_membership')->pluck('employee_id')->toArray();
      $administrativeIds = DB::table('administrative_council_membership')->pluck('employee_id')->toArray();
  
      // Find joint members (in both)
      $jointIds = array_intersect($academicIds, $administrativeIds);
      $jointCount = count($jointIds);
  
      // Remove joint members from each group
      $academicOnly = array_diff($academicIds, $jointIds);
      $administrativeOnly = array_diff($administrativeIds, $jointIds);
  
      $academicCouncilCount = count($academicOnly);
      $administrativeCouncilCount = count($administrativeOnly);
  
      return view('content.admin.dashboard', compact('totalUsers', 'academicCouncilCount', 'administrativeCouncilCount',  'jointCount'));
    }


    
  
}
