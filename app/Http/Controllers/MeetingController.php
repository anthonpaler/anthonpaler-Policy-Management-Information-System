<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meetings;
use App\Models\Venues;
use App\Models\Employee;

class MeetingController extends Controller
{
    public function viewMeetings(Request $request){

        return view('content.meetings.viewMeetings');
    }
    public function viewCreateMeeting(Request $request)
    {
        return view ('content.meetings.createMeeting');
    }
    public function createMeeting(Request $request )
    {
        $role = session('user_role');
        $creatorId = auth()->user()->id;
        $employeeId = auth()->user()->employee_id;
        $campus_id = Employee::where('id', $employeeId)->value('campus');
        $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));


        $request->validate([
            'description' => 'nullable|string',
            'quarter' => 'required|integer|unique_meeting_per_quarter:' . $request->input('year'). ',' . $level.','.$campus_id,
            'year' => 'required|integer',
            'modality' => 'nullable|integer',
            'venue' => 'nullable|string',
            'mode_if_online' => 'nullable|string',
            'link' => 'nullable|string',
            'council_type' => 'required|integer',
            'submission_start' => 'required|date|after_or_equal:today',
            'submission_end' => 'required|date|after:submission_start',
        ]);

        $meetingData = [
          'creator_id' => $creatorId,
          'campus_id' => $campus_id,
          'level' => $level,
          'description' => $request->input('description'),
          'meeting_date_time' => $request->input('meeting_date_time'),
          'quarter' => $request->input('quarter'),
          'year' => $request->input('year'),
          'venue' => $request->input('venue'),
          'status' => 0,
          'council_type' => $request->input('council_type'),
          'modality' => $request->input('modality') ?? 0,
          'mode_if_online' => $request->input('mode_if_online') ?? 0,
          'link' => $request->input(key: 'link'),
          'submission_start' => $request->input('submission_start'),
          'submission_end' => $request->input('submission_end'),
        ];

        Log::info('policy mis Log:' . __METHOD__, [
            'data' => [
                'meetingData' => $meetingData,
                'creator_id' => $creatorId,
                'campus_id' => Auth::user()->campus_id,
                'level' => $role == 3 ? 0 : 1,
            ],
            'route' => Route::currentRouteName(), // Gets the current route name
            'function' => __METHOD__, // Gets the calling method name
        ]);

        $meeting = Meetings::create($meetingData);
        $route_role = config('user_roles.role')[$role] ?? 'proponent';



            // Define role-based position filters
            $commonPositions = [
                'Assistant Professor I', 'Assistant Professor II', 'Assistant Professor III', 'Assistant Professor IV',
                'Associate Professor I', 'Associate Professor II', 'Associate Professor III', 'Associate Professor IV', 'Associate Professor V',
                'Professor I', 'Professor II', 'Professor III', 'Professor IV', 'Professor V', 'Professor VI'
            ];

            // Additional positions based on role
            $extraPositions = [
                4 => ['Local Secretary'],
                5 => ['Local Secretary', 'University Secretary']
            ];

            // Get user role
            $userRole = $role;
            $employeeCodes = collect();

            // Get all `empId` from `designation`
            $designationCodes = DB::table('designation')->pluck('empId');

            // Query workexperience for common academic positions
            $workExperienceCodes = DB::table('workexperience')
                ->whereIn('PositionTitleCode', $commonPositions)
                ->pluck('EmployeeCode');

            // Query secretaries table for secretarial positions
            $secretariesCodes = collect();
            if (isset($extraPositions[$userRole])) {
                $secretariesCodes = DB::table('secretaries')
                    ->whereIn('position', $extraPositions[$userRole])
                    ->pluck('employee_id');
            }
            
            // Merge designation and work experience codes
            switch ($request->input('council_type')) {
                case 3:
                    // Administrative council: use designation only
                    $employeeCodes = $designationCodes;
                    break;
            
                case 2:
                    // Academic council: use work experience
                    $employeeCodes = $workExperienceCodes;
                    break;
            
                default:
                    // Joint council: merge designation and work experience
                    $employeeCodes = $designationCodes->merge($workExperienceCodes);
                    break;
            }
            
            // For higher-level roles, merge in secretaries (once)
            if ($userRole >= 4) {
                $employeeCodes = $employeeCodes->merge($secretariesCodes)->unique();
            }
            
            // Filter employees based on campus_id if user role is 3
            $employeesQuery = Employee::whereIn('id', $employeeCodes);
            if ($userRole == 3) {
                $employeesQuery->where('campus', $campus_id);
            }

            // Fetch email addresses
            $emails = $employeesQuery->pluck('EmailAddress');

            // Send emails
            foreach ($emails as $email) {
              Mail::to($email)->send(new MeetingCreatedMail($meetingData));
            }


        return response()->json([
            'type' => "success",
            'title' => "Success",
            'redirect' => route($route_role.'.meetings'),
            'message' => 'Meeting created successfully.',
        ]);
    }

    public function viewEditMeeting(Request $request)
    {
        return view ('content.meetings.editMeeting');
    }
}

