<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalCouncilMeeting;
use App\Models\UniversityCouncilMeeting;
use App\Models\BorMeeting;

use App\Models\Venues;
use App\Models\Employee;

class MeetingController extends Controller
{
    // VIEW MEETINGS
    public function viewMeetings(Request $request){
        $role = session('user_role');
        $employeeId = session('employee_id');
        $campus_id = session('campus_id');
        $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));
        
        if (session('isProponent')) {
            $allowedCouncilTypes = [1];
            if ($role == 0) {
                $allowedCouncilTypes = [1, 2];
            } elseif ($role == 1) {
                $allowedCouncilTypes = [1, 3];
            } elseif ($role == 2) {
                $allowedCouncilTypes = [1, 2, 3];
            }
            $meetings = LocalCouncilMeeting::where('campus_id', $campus_id)
            ->whereIn('council_type', $allowedCouncilTypes)
            ->withCount(['proposals' => function ($query) use ($employeeId) {
                $query->whereHas('proponents', function ($q) use ($employeeId) {
                    $q->where('proposal_proponents.employee_id', $employeeId);
                });
            }])            
            ->orderBy('created_at', 'desc')
            ->get();

            // dd($meetings->toArray()); 
        }
        if($role == 3 && $level == 0){
            $meetings = LocalCouncilMeeting::where('campus_id', $campus_id)
            ->orderBy('created_at', 'desc')
            ->get();
        }
       
        if($role == 4 && $level == 1){
            $meetings = UniversityCouncilMeeting::orderBy('created_at', 'desc')->get();
        }

        if($role == 5 && $level == 2){
            $meetings = BorMeeting::orderBy('created_at', 'desc')->get();
        }
       

        return view('content.meetings.viewMeetings', compact('meetings', 'level'));
    }

    // VIEW CREATE MEETING PAGE
    public function viewCreateMeeting(Request $request)
    {
        $campus_id = session('campus_id');

        $venues = Venues::where('campus_id', $campus_id)->get();

        return view ('content.meetings.createMeeting', compact('venues'));
    }

    // CREATE THE MEETING
    public function createMeeting(Request $request )
    {
        $role = session('user_role');
        $employeeId = session('employee_id');
        $campus_id = session('campus_id');
        $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));
        

        $request->validate([
            'description' => 'nullable|string',
            'quarter' => 'required|integer|unique_meeting_per_quarter:' . $request->input('year'). ',' . $level.','.$campus_id.','.$request->input('council_type'),
            'year' => 'required|integer',
            'modality' => 'nullable|integer',
            'venue' => 'nullable|string',
            'mode_if_online' => 'nullable|string',
            'link' => 'nullable|url',
            'council_type' => 'required|integer',
            'submission_start' => 'required|date|after_or_equal:today',
            'submission_end' => 'required|date|after:submission_start',
        ]);

        $meetingData = [
          'creator_id' => $employeeId,
          'description' => $request->input('description'),
          'meeting_date_time' => $request->input('meeting_date_time'),
          'quarter' => $request->input('quarter'),
          'year' => $request->input('year'),
          'venue_id' => $request->input('venue'),
          'status' => 0,
          'council_type' => $request->input('council_type'),
          'modality' => $request->input('modality') ?? 0,
          'mode_if_online' => $request->input('mode_if_online') ?? 0,
          'link' => $request->input(key: 'link'),
          'submission_start' => $request->input('submission_start'),
          'submission_end' => $request->input('submission_end'),
        ];
        
        

        
        if($level == 0){
            $meetingData['campus_id'] = $campus_id;
            $meeting = LocalCouncilMeeting::create($meetingData);
        }
       
        if($level == 1){
            $meeting = UniversityCouncilMeeting::create($meetingData);
        }

        if($level == 2){
            $meeting = BorMeeting::create($meetingData);
        }
       
       
        return response()->json([
            'type' => "success",
            'title' => "Success",
            'redirect' => route(getUserRole().'.meetings'),
            'message' => 'Meeting created successfully.',
        ]);
    }

    // VIEW EDIT MEETING
    public function viewEditMeeting(Request $request, String $level, String $meeting_id)
    {
        $campus_id = session('campus_id');

        $venues = Venues::where('campus_id', $campus_id)->get();


        $meetingID = decrypt($meeting_id);
        if($level == 'Local'){
            $meeting = LocalCouncilMeeting::find($meetingID);
        }
        if($level == 'University'){
            $meeting = UniversityCouncilMeeting::find($meetingID);
        }
        if($level == 'BOR'){
            $meeting = BorMeeting::find($meetingID);
        }

        // dd($venues);
        return view ('content.meetings.editMeeting', compact('meeting', 'venues'));
    }
    
    // EDIT MEETING
    public function EditMeeting(Request $request, String $level, String $meeting_id)
    {
        $request->validate([
            'description' => 'nullable|string',
            'modality' => 'nullable|integer|min:0|max:2',
            'venue' => 'nullable|string',
            'link' => 'nullable|url',
            'status' => 'required|integer',
            'mode_if_online' => 'nullable|string',
            'council_type' => 'required|integer', 
            'year' => 'required',
            'submission_start' => 'required|date',
            'submission_end' => 'required|date|after_or_equal:submission_start',
        ]);

        $meetingID = decrypt($meeting_id);

        if($level == 'Local'){
            $meeting = LocalCouncilMeeting::find($meetingID);
        }
        if($level == 'University'){
            $meeting = UniversityCouncilMeeting::find($meetingID);
        }
        if($level == 'BOR'){
            $meeting = BorMeeting::find($meetingID);
        }

        $meetingData = [
            'description' => $request->input('description'),
            'meeting_date_time' => $request->input('meeting_date_time'),
            'venue_id' => $request->input('venue'),
            'council_type' => $request->input('council_type'),
            'modality' => $request->input('modality')?? 0 ,
            'mode_if_online' => $request->input('mode_if_online'),
            'status' => $request->input('status'),
            'submission_start' => $request->input('submission_start'),
            'submission_end' => $request->input('submission_end'),
            'link' => $request->input('link'),
            'year' => $request->input('year'),
        ];

        $meeting->update($meetingData);

        return response()->json([
            'type' => "success",
            'title' => "Success",
            'message' => 'Meeting updated successfully.',
        ]);
    }

    // VIEW MEETING DETAILS
    public function viewMeetingDetails(Request $request, String $level, String $meeting_id)
    {
        $meetingID = decrypt($meeting_id);
        
        if($level == 'Local'){
            $meeting = LocalCouncilMeeting::find($meetingID);
        }
        if($level == 'University'){
            $meeting = UniversityCouncilMeeting::find($meetingID);
        }
        if($level == 'BOR'){
            $meeting = BorMeeting::find($meetingID);
        }

        // dd( $meeting);

        return view('content.meetings.viewMeetingDetails', compact('meeting'));
    }

    // FILTER MEETINGS
    public function filterMeetings(Request $request){
        $role = session('user_role');
        $employeeId = session('employee_id');
        $campus_id = session('campus_id');
        // $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));

        $request->validate([
            // 'year' => 'required|string',
            'level' => 'required|integer',
        ]);

        $meetingLevel = $request->input('level');

        if (session('isProponent')) {

            if($meetingLevel == 0){
                // $meetings = LocalCouncilMeeting::where('campus_id', $campus_id)
                // ->orderBy('created_at', 'desc')
                // ->get();

                $allowedCouncilTypes = [1];
                if ($role == 0) {
                    $allowedCouncilTypes = [1, 2];
                } elseif ($role == 1) {
                    $allowedCouncilTypes = [1, 3];
                } elseif ($role == 2) {
                    $allowedCouncilTypes = [1, 2, 3];
                }
                $meetings = LocalCouncilMeeting::where('campus_id', $campus_id)
                ->whereIn('council_type', $allowedCouncilTypes)
                ->withCount(['proposals' => function ($query) use ($employeeId) {
                    $query->where('employee_id', $employeeId); 
                }])
                ->orderBy('created_at', 'desc')
                ->get();
            }
           
            if($meetingLevel == 1){
                $meetings = UniversityCouncilMeeting::orderBy('created_at', 'desc')->get();
            }
    
            if($meetingLevel == 2){
                $meetings = BorMeeting::orderBy('created_at', 'desc')->get();
            }

        }else{
            if($meetingLevel == 0){
                $meetings = LocalCouncilMeeting::where('campus_id', $campus_id)
                ->orderBy('created_at', 'desc')
                ->get();
            }
           
            if($meetingLevel == 1){
                $meetings = UniversityCouncilMeeting::orderBy('created_at', 'desc')->get();
            }
    
            if($meetingLevel == 2){
                $meetings = BorMeeting::orderBy('created_at', 'desc')->get();
            }
        }
       

        // dd($meetings);
        return response()->json([
            'type' => 'success',
            'html' => view('content.meetings.partials.meetings_table', compact('meetings'))->render()
        ]);
    }
}

