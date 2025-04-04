<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalCouncilMeeting;
use App\Models\UniversityCouncilMeeting;
use App\Models\BorMeeting;
use Illuminate\Support\Facades\DB;
use App\Models\Venues;
use App\Models\UniversityMeetingAgenda;
use App\Models\BoardMeetingAgenda;
use App\Models\LocalMeetingAgenda;
use App\Models\Employee;
use App\Models\HrmisEmployee;
use Illuminate\Support\Facades\Mail;
use App\Mail\MeetingNotification;
use App\Mail\MeetingUpdateNotification;
use Carbon\Carbon;




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
        

        return view ('content.meetings.createMeeting');
    }

    // CREATE THE MEETING
    public function createMeeting(Request $request )
    {
        set_time_limit(300);// Increase execution time to 5 minutes for this request

        DB::beginTransaction();
        try{
            $role = session('user_role');
            $employeeId = session('employee_id');
            $campus_id = session('campus_id');
            $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));


            $request->validate([
                'description' => 'nullable|string',
                'quarter' => 'required|integer|unique_meeting_per_quarter:' . $request->input('year'). ',' . $level.','.$campus_id.','.$request->input('council_type'),
                'year' => 'required|integer',
                'modality' => 'nullable|integer',
                'venue' => 'nullable|string|max:150',
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
              'venue' => $request->input('venue'),
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

            $employeeQuery = Employee::query();


            if ($request->input('council_type') == 2) { // Academic Council
                $employeeQuery->whereIn('id', function($query) {
                    $query->select('employee_id')->from('academic_council_membership');
                });
            } elseif ($request->input('council_type') == 3) { // Administrative Council
                $employeeQuery->whereIn('id', function($query) {
                    $query->select('employee_id')->from('administrative_council_membership');
                });
            } else { // Joint Council (Both Academic and Administrative Council)
                $employeeQuery->whereIn('id', function($query) {
                    $query->select('employee_id')->from('academic_council_membership')
                          ->union(
                              DB::table('administrative_council_membership')->select('employee_id')
                          );
                });
            }

            // Filter by campus if needed
            if ($level == 0) {
                $employeeQuery->where('campus', $campus_id);
            }

            // Get email addresses of employees
            $emails = $employeeQuery->pluck('EmailAddress',)->toArray();
            $cellNumbers = $employeeQuery->pluck('Cellphone')->toArray();


            // Send emails in smaller batches
            $chunks = array_chunk($emails, 50); // Send 50 at a time


            // Send meeting notification emails
            // foreach ($chunks as $batch) {
            //     Mail::to($batch)->send(new MeetingNotification($meeting));
            //     sleep(2); // Wait 2 seconds between batches to avoid timeouts
            // }
            // foreach ($emails as $email) {
            //     Mail::to($email)->send(new MeetingNotification($meeting));
            // }


            // 🔹 Send SMS Notifications
            //   $smsController = new SMSController();
            //   $quarter = config('meetings.quaterly_meetings')[$request->input('quarter')] ?? '';
            //   $level = config('meetings.level')[$level] ?? '';
            //   $councilType = config('meetings.council_types')[strtolower($level) . '_level'][$request->input('council_type')] ?? '';
            //   $meetingDateTime = date('M j, Y g:i A', strtotime($request->input('meeting_date_time')));


            //   $message = "ADVISORY!\nThe $quarter – $councilType\nwill be on $meetingDateTime.\nfor more details please visit https://policy.southernleytestateu.edu.ph";

            //   foreach ($emails as $index => $email) {
            //     $phone = $cellNumbers[$index] ?? null;

            //     // If no cellphone in `employees` table, check `hrmis.employee` table
            //     if (empty($phone)) {
            //         $hrmisEmployee = HrmisEmployee::where('EmailAddress', $email)->first();
            //         $phone = $hrmisEmployee?->Cellphone;
            //     }

            //     // Send SMS if phone number is found
            //     if (!empty($phone)) {
            //         $smsResponse = $smsController->send($phone, $message);
            //         if ($smsResponse['Error'] == 1) {
            //             \Log::error("SMS Failed to $phone: " . $smsResponse['Message']);
            //         }
            //     }
            // }


            DB::commit();

            return response()->json([
                'type' => "success",
                'title' => "Success",
                'redirect' => route(getUserRole().'.meetings'),
                'message' => 'Meeting created successfully and an email notification has been sent to the Council Members.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction if something fails

            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }

    // VIEW EDIT MEETING
    public function viewEditMeeting(Request $request, String $level, String $meeting_id)
    {
        $campus_id = session('campus_id');



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
        return view ('content.meetings.editMeeting', compact('meeting'));
    }

    // EDIT MEETING
    public function EditMeeting(Request $request, String $level, String $meeting_id)
    {
        DB::beginTransaction();

        try{
            $request->validate([
                'description' => 'nullable|string',
                'modality' => 'nullable|integer|min:0|max:2',
                'venue' => 'nullable|string|max:150',
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

            $updatedFields = [];

            // Normalize date formats before comparison
            $oldDateTime = $meeting->meeting_date_time ? Carbon::parse($meeting->meeting_date_time)->format('Y-m-d H:i:s') : null;
            $newDateTime = $request->input('meeting_date_time') ? Carbon::parse($request->input('meeting_date_time'))->format('Y-m-d H:i:s') : null;


            if ($oldDateTime !== $newDateTime) {
                $updatedFields['meeting_date_time'] = [
                    'before' => $oldDateTime,
                    'after' => $newDateTime
                ];
            }

            $oldSubmissionStart = $meeting->submission_start ? Carbon::parse($meeting->submission_start)->format('Y-m-d') : null;
            $newSubmissionStart = $request->input('submission_start') ? Carbon::parse($request->input('submission_start'))->format('Y-m-d') : null;

            if ($oldSubmissionStart !== $newSubmissionStart) {
                $updatedFields['submission_start'] = [
                    'before' => $oldSubmissionStart,
                    'after' => $newSubmissionStart
                ];
            }

            $oldSubmissionEnd = $meeting->submission_end ? Carbon::parse($meeting->submission_end)->format('Y-m-d') : null;
            $newSubmissionEnd = $request->input('submission_end') ? Carbon::parse($request->input('submission_end'))->format('Y-m-d') : null;

            if ($oldSubmissionEnd !== $newSubmissionEnd) {
                $updatedFields['submission_end'] = [
                    'before' => $oldSubmissionEnd,
                    'after' => $newSubmissionEnd
                ];
            }




            $meetingData = [
                'description' => $request->input('description'),
                'meeting_date_time' => $request->input('meeting_date_time'),
                'venue' => $request->input('venue'),
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


            // Send email & SMS only if relevant fields were updated
        if (!empty($updatedFields)) {
            // Get email addresses of employees
            $employeeQuery = Employee::query();

            if ($request->input('council_type') == 2) { // Academic Council
                $employeeQuery->whereIn('id', function($query) {
                    $query->select('employee_id')->from('academic_council_membership');
                });
            } elseif ($request->input('council_type') == 3) { // Administrative Council
                $employeeQuery->whereIn('id', function($query) {
                    $query->select('employee_id')->from('administrative_council_membership');
                });
            } else { // Joint Council (Both Academic and Administrative Council)
                $employeeQuery->whereIn('id', function($query) {
                    $query->select('employee_id')->from('academic_council_membership')
                          ->union(
                              DB::table('administrative_council_membership')->select('employee_id')
                          );
                });
            }

            if ($level == 'Local') {
                $employeeQuery->where('campus', session('campus_id'));
            }

            $emails = $employeeQuery->pluck('EmailAddress')->toArray();
            $cellNumbers = $employeeQuery->pluck('Cellphone')->toArray();

            // Send email notifications in batches
            $chunks = array_chunk($emails, 50);
            foreach ($chunks as $batch) {
                Mail::to($batch)->send(new MeetingUpdateNotification($meeting,$updatedFields));
                sleep(2);
            }

            // // 🔹 Send SMS Notifications
            // $smsController = new SMSController();
            // $quarter = config('meetings.quaterly_meetings')[$meeting->quarter] ?? 'N/A';
            // $councilType = config('meetings.council_types')[strtolower($level) . '_level'][$request->input('council_type')] ?? '';

            // $message = "ADVISORY!\nThere are some changes on $quarter $councilType meeting,please visit https://policy.southernleytestateu.edu.ph";

            // foreach ($emails as $index => $email) {
            //     $phone = $cellNumbers[$index] ?? null;
            //     if (empty($phone)) {
            //         $hrmisEmployee = HrmisEmployee::where('EmailAddress', $email)->first();
            //         $phone = $hrmisEmployee?->Cellphone;
            //     }
            //     if (!empty($phone)) {
            //         $smsResponse = $smsController->send($phone, $message);
            //         if ($smsResponse['Error'] == 1) {
            //             \Log::error("SMS Failed to $phone: " . $smsResponse['Message']);
            //         }
            //     }
            // }
        }

        DB::commit();

            return response()->json([
                'type' => "success",
                'title' => "Success",
                'message' => 'Meeting updated successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
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

    // MY PROPOSALS IN MEETINGS - PROPONENT
    public function viewMyProposalsInMeeting($level, $meeting_id)
    {
        $meetingID = decrypt($meeting_id);
        $proposals = collect(); // Initialize as an empty collection
        $meeting = null; // Initialize the meeting variable

        if ($level == 'Local') {
            $meeting = LocalCouncilMeeting::find($meetingID);
            $proposals = LocalMeetingAgenda::where("local_council_meeting_id", $meetingID)
                ->whereHas('proposal.proponents', function ($query) {
                    $query->where('proposal_proponents.employee_id', session('employee_id'));
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($level == 'University') {
            $meeting = UniversityCouncilMeeting::find($meetingID);
            $proposals = UniversityMeetingAgenda::where("university_meeting_id", $meetingID)
                ->whereHas('proposal.proponents', function ($query) {
                    $query->where('proposal_proponents.employee_id', session('employee_id'));
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($level == 'BOR') {
            $meeting = BorMeeting::find($meetingID);
            $proposals = BoardMeetingAgenda::where("bor_meeting_id", $meetingID)
                ->whereHas('proposal.proponents', function ($query) {
                    $query->where('proposal_proponents.employee_id', session('employee_id'));
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }
        // dd($proposals);
        return view('content.proposals.myProposalsInMeeting', compact('proposals', 'meeting'));
    }

    // FILTER MEETINGS
    public function filterMeetings(Request $request){
        try{
            $role = session('user_role');
            $employeeId = session('employee_id');
            $campus_id = session('campus_id');


            $request->validate([
                'level' => 'required|integer',
            ]);

            $meetingLevel = $request->input('level');

            if (session('isProponent')) {

                if($meetingLevel == 0){
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
                }

                if($meetingLevel == 1){
                    $meetings = UniversityCouncilMeeting::withCount(['proposals' => function ($query) use ($employeeId) {
                        $query->whereHas('proponents', function ($q) use ($employeeId) {
                            $q->where('proposal_proponents.employee_id', $employeeId);
                        });
                    }])
                    ->orderBy('created_at', 'desc')
                    ->get();
                }

                if($meetingLevel == 2){
                    $meetings = BorMeeting::withCount(['proposals' => function ($query) use ($employeeId) {
                        $query->whereHas('proponents', function ($q) use ($employeeId) {
                            $q->where('proposal_proponents.employee_id', $employeeId);
                        });
                    }])
                    ->orderBy('created_at', 'desc')
                    ->get();
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
        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }
}

