<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\LocalCouncilMeeting;
use App\Models\UniversityCouncilMeeting;
use App\Models\BorMeeting;
use App\Models\User;
use App\Models\Proposal;
use App\Models\LocalMeetingAgenda;
use App\Models\UniversityMeetingAgenda;
use App\Models\BoardMeetingAgenda;
use Illuminate\Support\Facades\Schema;

class Analytics extends Controller
{
    public function secretaryDashboard()
    {
      $level = session('secretary_level');
      $campusId = session('campus_id');
      $meetings = collect();
      $upperMeetings = collect();

      $meetingModel = [
        0 => LocalCouncilMeeting::class,
        1 => UniversityCouncilMeeting::class,
        2 => BorMeeting::class,
      ];

      // FOR SAME LEVEL MEETINGS
      if( $level == 0){
        $latestMeetingRecord = $meetingModel[$level]::where('campus_id', $campusId)->orderByDesc('created_at')->first();
      }else{
        $latestMeetingRecord = $meetingModel[$level]::orderByDesc('created_at')->first();
      }
      $latest_quarter = $latestMeetingRecord->quarter ?? null;
      $latest_year = $latestMeetingRecord->year ?? null;

      if( $level == 0){
        $meetings =$meetingModel[$level]::where('campus_id', $campusId)->where('year', $latest_year)->where('quarter', $latest_quarter)->get();
      }else{
        $meetings =$meetingModel[$level]::where('year', $latest_year)->where('quarter', $latest_quarter)->get();
      }

      // FOR UPPER LEVEL MEETINGS
      $latestUpperMeetingRecord = $meetingModel[$level+1]::orderByDesc('created_at')->first();

      $latest_upper_quarter = $latestUpperMeetingRecord->quarter ?? null;
      $latest_upper_year = $latestUpperMeetingRecord->year ?? null;

      $upperMeetings =$meetingModel[$level+1]::where('year', $latest_upper_year)->where('quarter', $latest_upper_quarter)->get();

      // dd($meetings, $upperMeetings);
      return view('content.dashboard.secretary.dashboard', compact('meetings', 'upperMeetings'));
    }

    public function proponentDashboard()
    {
      $role = session('user_role');
      $campusId = session('campus_id');
      $meetings = collect();

      $allowedCouncilTypes = [1];
      if ($role == 0) {
          $allowedCouncilTypes = [1, 2];
      } elseif ($role == 1) {
          $allowedCouncilTypes = [1, 3];
      } elseif ($role == 2) {
          $allowedCouncilTypes = [1, 2, 3];
      }

      $latestMeetingRecord = LocalCouncilMeeting::where('campus_id', $campusId)
        ->whereIn('council_type', $allowedCouncilTypes)
        ->orderByDesc('created_at')
        ->first();

      $meetings = LocalCouncilMeeting::where('campus_id', $campusId)
        ->where('year', $latest_year)
        ->where('quarter', $latest_quarter)
        ->whereIn('council_type', $allowedCouncilTypes)
        ->get();

      return view('content.dashboard.proponent.dashboard', compact('meetings'));
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
