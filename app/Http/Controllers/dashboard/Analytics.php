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
    public function index()
    {
        $role = session('user_role');  
        $campusId = session('campus_id'); // Ensure this exists before using it
        $meetings = collect(); // Default empty collection
        $users = User::all(); // Get all users
        $meeting = [];
        $upperMeeting = [];

       // Check if campus_id exists in the Local Council Meetings table
       $hasCampusIdColumn = Schema::hasColumn('local_council_meetings', 'campus_id');

       if ($role == 3) { // Local Secretary
           if ($hasCampusIdColumn) {
               $localMeeting = LocalCouncilMeeting::where('campus_id', $campusId)
                   ->where(function ($query) {
                       $query->whereNotNull('link')
                             ->orWhereNotNull('mode_if_online');
                   })
                   ->orderBy('meeting_date_time', 'desc')
                   ->first(); // Get only one record
               
               // Convert to collection
               $localMeetings = $localMeeting ? collect([$localMeeting]) : collect();
           } else {
               $localMeetings = collect();
           }
       
           // Fetch University Meetings (if applicable)
           $universityMeetings = UniversityCouncilMeeting::where(function ($query) {
                   $query->whereNotNull('link')
                         ->orWhereNotNull('mode_if_online');
               })
               ->latest('meeting_date_time')
               ->get(); // ✅ Fetch university meetings
       
           // Merge both collections and sort
           $meeting = $localMeetings->first();

           $upperMeeting = $universityMeetings->first();
        //    dd($meeting, $upperMeeting);
       
       } elseif (session('isProponent')) {
        $localMeeting = LocalCouncilMeeting::where('campus_id', $campusId)
            ->where(function ($query) {
                $query->whereNotNull('link')
                      ->orWhereNotNull('mode_if_online');
            })
            ->latest('meeting_date_time', 'desc')
            ->first(); // Correct method: first() to get only one record
    
        // If you want to assign the result to $upperMeeting
        $upperMeeting = $localMeeting;
    
    
        //    dd($upperMeeting);
       
       } elseif ($role == 4) {
           // University Secretary - Only their own created meetings
           $meeting = UniversityCouncilMeeting::where(function ($query) {
                   $query->whereNotNull('link')
                         ->orWhereNotNull('mode_if_online');
               })
               ->latest('meeting_date_time')
               ->first(); // ✅ Keep collection

               $upperMeeting = BorMeeting::where(function ($query) {
                $query->whereNotNull('link')
                      ->orWhereNotNull('mode_if_online');
            })
            ->latest('meeting_date_time')
            ->first(); // ✅ Keep collection
       
       } elseif ($role == 5) {
           // BOR Secretary - Only their own created meetings   
           $meeting = BorMeeting::where(function ($query) {
                   $query->whereNotNull('link')
                         ->orWhereNotNull('mode_if_online');
               })
               ->latest('meeting_date_time')
               ->first(); // ✅ Keep collection

            $upperMeeting = $meeting;
       }       

      // Get the first meeting from the collection
    //       // Count total proposals submitted by users within the same campus
    //       $proposalsCount = Proposal::with('proponents')
    // ->whereHas('proponents', function ($query) use ($campusId) {
    //     $query->where('campus_id', $campusId);
    // })
    //     ->when(session('user_role') == 3, function ($query) {
    //         // Local Secretary: View only the latest proposal per user
    //         $query->latest('created_at');
    //     })
    //     ->when(session('user_role') == 4, function ($query) {
    //         // University Secretary: View only endorsed proposals
    //         $query->where('status', 5);
    //     })
    //     ->count();
    
        $returnedProposalCount = Proposal::where('status', 2)
        ->whereHas('proponents', function ($query) use ($campusId) {
            $query->where('campus_id', $campusId);
        })
        ->count();
    
        // dd($meetings);

        $endorsedProposalCount = Proposal::where('status', 1)
        ->whereHas('proponents', function ($query) use ($campusId) {
            $query->where('campus_id', $campusId);
        })
        ->count();    
        $deferredProposalCount = Proposal::where('status', 7)
        ->whereHas('proponents', function ($query) use ($campusId) {
            $query->where('campus_id', $campusId);
        })
        ->count();
    
          
        $latestProposal = Proposal::whereHas('proponents', function ($query) {
            $query->where('users.id', auth()->id());
        })
        ->latest('created_at')
        ->first();       

        $userProposalCount = Proposal::whereHas('proponents', function ($query) {
            $query->where('users.id', auth()->id());
        })
        ->count();
    
          // Count posted to agenda proposals
        $postedToAgendaCount = Proposal::where('status', 1)->count();
        $coletillaCount = Proposal::where('status', 5)->count();
        $endorseColletillaCount = Proposal::where('status', 6)->count();
          
       
        return view('content.dashboard.dashboards-analytics', compact('meeting', 'upperMeeting', 'users', 'returnedProposalCount', 'endorsedProposalCount', 'latestProposal', 'userProposalCount', 'deferredProposalCount', 'postedToAgendaCount', 'coletillaCount', 'endorseColletillaCount')); 	
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
