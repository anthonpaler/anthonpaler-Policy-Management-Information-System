<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class Analytics extends Controller
{
    public function index()
    {

      $user = Auth::user();
      $roles = session('available_roles') ?? []; // this is the string array for dropdown

      $role = session('user_role'); // this is the numeric active role
      $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));

      // dd($role);
      if(in_array($role, [0,1,2,6])){

      }else if(in_array($role, [3, 4])){

      }else if ($role == 5){

      }


      return view('content.dashboard.dashboards-analytics', compact('roles', 'role', 'level'));

  }

    public function switchRole(Request $request)
    {
      $user = Auth::user();

      $selectedRole = $request->input('role');

      if (!$user || !$selectedRole) {
          return response()->json(['success' => false, 'message' => 'Invalid request'], 400);
      }

      // Map role names to role codes
      $roleMap = [
          'Local Secretary' => 3,
          'University Secretary' => 4,
          'Board Secretary' => 5
      ];

      if (!array_key_exists($selectedRole, $roleMap)) {
          return response()->json(['success' => false, 'message' => 'Invalid role selected'], 400);
      }

      $newRole = $roleMap[$selectedRole];

      // Update session with new role
      session()->put('user_role', $newRole);
      $secretaryLevel = match ($newRole) {
        3 => 0,
        4 => 1,
        5 => 2,
        default => null,
    };

      session()->put('secretary_level', $secretaryLevel);
      session()->save(); //

      $user->update(['role' => $newRole]);


      // Determine redirection based on the new role
      $redirectRoute = match ($roleMap[$selectedRole]) {
          3 => route('local_sec.dashboard'),
          4 => route('univ_sec.dashboard'),
          5 => route('board_sec.dashboard'),
          default => route('proponent.dashboard'),
      };

      return response()->json(['success' => true, 'redirect' => $redirectRoute]);
    }
}
