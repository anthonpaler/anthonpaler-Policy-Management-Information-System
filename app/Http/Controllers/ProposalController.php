<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalCouncilMeeting;
use App\Models\UniversityCouncilMeeting;
use App\Models\BorMeeting;
use App\Models\Employee;
use App\Models\Proposal;
use App\Models\ProposalFile;
use App\Models\ProposalLog;
use App\Models\LocalMeetingAgenda;
use App\Models\UniversityMeetingAgenda;
use App\Models\BoardMeetingAgenda;
use App\Models\ProposalProponent;
use App\Models\OtherMatter;
use App\Models\LocalOob;
use App\Models\UniversityOob;
use App\Models\BoardOob;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Mail\ProposalSubmissionNotification;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\SMSController;


class ProposalController extends Controller
{
    public function viewSubmitProposal(Request $request, String $level, String $meeting_id)
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

        return view('content.proposals.submitProposal', compact('meeting'));
    }

    public function searchUsers(Request $request)
    {
        $query = trim($request->input('query'));

        $campus_id = session('campus_id');

        $userRole = session('user_role');
        $roles = match ($userRole) {
            0 => [0, 2, 6],
            1 => [1, 2, 6],
            2 => [0, 1, 2, 6],
            3 => [0, 1, 2, 6],
            4 => [0, 1, 2, 6],
            5 => [0, 1, 2, 6],
            6 => [0, 1, 2, 6],
            default => [],
        };

        if (empty($roles)) {
            return response()->json([]);
        }


        if(session('isProponent')){
            if (!$campus_id) {
                return response()->json([]);
            }

            $users = User::whereIn('role', $roles)
                ->whereHas('employee', function ($q) use ($campus_id) {
                    $q->where('campus', $campus_id);
                })
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->get();
        }

        if(session('isSecretary')){
            $users = User::whereIn('role', $roles)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%");
            })
           ->get();
        }

        return response()->json($users);
    }


    // SUBMIT PROPOSAL FOR PROPONENT (PROPONENT CAN ONLY SUBMIT IN LOCAL MEETING)
    public function submitProposal(Request $request, String $meeting_id)
    {
        DB::beginTransaction(); // Start transaction

        try {
            $meetingID = decrypt($meeting_id);
            $meeting = LocalCouncilMeeting::find($meetingID);

            if ($meeting->getIsSubmissionClosedAttribute() || ($meeting->status == 1)) {
                return response()->json([
                    'type' => 'danger',
                    'message' => 'The submission or the meeting is already closed!',
                    'title' => "Meeting Closed!"
                ]);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'action' => 'required|string|max:255',
                'proposal_files' => 'required|array',
                'proposal_files.*' => 'file|mimes:pdf,xls,xlsx,csv|max:100000',
                'proponents' => 'required',
                'matter' => 'required|integer',
                'sub_type' => 'nullable|integer',
            ]);

            $campus_id = session('campus_id');

            // Create proposal
            $proposal = Proposal::create([
                'campus_id' => $campus_id,
                'title' => $request->input('title'),
                'action' => $request->input('action'),
                'type' => $request->input('matter'),
                'sub_type' => $request->input('sub_type'),
                'status' => 0,
            ]);

            // Insert into LocalMeetingAgenda
            LocalMeetingAgenda::create([
                'local_council_meeting_id' => $meetingID,
                'local_proposal_id' => $proposal->id,
                'status' => 0,
            ]);

            // Handle file uploads
            $fileIds = [];
            $file_order_no = 1;

            if ($request->hasFile('proposal_files')) {
                foreach ($request->file('proposal_files') as $file) {
                    $originalNameWithExt = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                    preg_match('/^(.*?)(\(\d+\))?(\.\w+)?$/', $originalNameWithExt, $matches);
                    $baseName = trim($matches[1]);
                    $filename = "{$baseName}.{$extension}";

                    $i = 1;
                    while (Storage::disk('public')->exists("proposals/{$filename}")) {
                        $filename = "{$baseName} ({$i}).{$extension}";
                        $i++;
                    }

                    $filePath = $file->storeAs('proposals', $filename, 'public');

                    $proposalFile = ProposalFile::create([
                        'proposal_id' => $proposal->id,
                        'file' => $filename,
                        'version' => 1,
                        'file_status' => 1,
                        'file_reference_id' => null,
                        'is_active' => true,
                        'order_no' => $file_order_no,
                    ]);

                    $fileIds[] = $proposalFile->id;
                    $file_order_no++;
                }
            }

            // Save file IDs in proposal logs
            $fileIdsString = implode(',', $fileIds);

            ProposalLog::create([
                'proposal_id' => $proposal->id,
                'employee_id' => session('employee_id'),
                'comments' => null,
                'status' => 0,
                'level' => 0,
                'action' => 7,
                'file_id' => $fileIdsString,
            ]);

            $proponentIds = explode(',', $request->input('proponents'));

            foreach ($proponentIds as $employeeId) {
                ProposalProponent::create([
                    'proposal_id' => $proposal->id,
                    'employee_id' => trim($employeeId),
                ]);
            }


             // ✅ Notify the Meeting Creator via Email & SMS
            $creator = $meeting->creator; // Fetching the creator details
            if ($creator) {
                // Send Email Notification
                Mail::to($creator->EmailAddress)->send(new ProposalSubmissionNotification($proposal, $meeting));

                // // Send SMS Notification
                // $smsController = new SMSController();
                // $message = "A new proposal '{$proposal->title}' has been submitted for review in {$meeting->description}. Please check the system.";

                // if (!empty($creator->Cellphone)) {
                //     $smsResponse = $smsController->send($creator->Cellphone, $message);
                //     if ($smsResponse['Error'] == 1) {
                //         \Log::error("SMS Failed to {$creator->Cellphone}: " . $smsResponse['Message']);
                //     }
                // }
            }



            DB::commit(); // Commit transaction if everything is successful

            return response()->json([
                'type' => 'success',
                'message' => 'Proposal submitted successfully!',
                'title' => "Success!"
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

    public function fetchProponents(Request $request)
    {
      $query = $request->input('search');

      $users = User::whereIn('role', [0, 1, 2])
                   ->where('email', 'LIKE', "%{$query}%")
                   ->pluck('email')
                   ->filter(function ($email) {
                       return filter_var($email, FILTER_VALIDATE_EMAIL);
                   })
                   ->values(); // Ensure it returns an indexed array

      return response()->json($users);
    }


    public function addProposal(Request $request, String $meeting_id)
    {
        DB::beginTransaction();
        try {
            $proponent_email = is_array($request->proponent_email) ? $request->proponent_email[0] : $request->proponent_email;
            $request->merge(['proponent_email' => $proponent_email]);

            $meetingID = decrypt($meeting_id);
            $userRole = session('user_role');

            // Ensure the user is a secretary (Local = 3, University = 4, Board = 5)
            if (!in_array($userRole, [3, 4, 5])) {
                return response()->json(['type' => 'danger', 'message' => 'You are not authorized to submit a proposal!', 'title' => "Unauthorized"]);
            }

            // Determine meeting type
            $meeting = $this->getMeetingModel($meetingID, $userRole);

            if (!$meeting) {
                return response()->json(['type' => 'danger', 'message' => 'Invalid meeting ID.', 'title' => "Error"]);
            }

            // if ($meeting->getIsSubmissionClosedAttribute() || ($meeting->status == 1)) {
            //     return response()->json(['type' => 'danger', 'message' => 'The submission or the meeting is already closed!', 'title' => "Meeting Closed!"]);
            // }

            $request->validate([
                'proponent_email' => 'required|email|exists:employees,EmailAddress',
                'title' => 'required|string|max:255',
                'action' => 'required|string|max:255',
                'proposal_files' => 'required|array',
                'proposal_files.*' => 'file|mimes:pdf,xls,xlsx,csv|max:100000',
                'matter' => 'required|integer',
                'sub_type' => 'nullable|integer',
            ]);


            $proponent = Employee::where('EmailAddress', $request->input('proponent_email'))->first();

            $campus_id = session('campus_id');


            // Create proposal
            $proposal = Proposal::create([
                'employee_id' => $proponent->id,
                'campus_id' => $campus_id,
                'title' => $request->input('title'),
                'action' => $request->input('action'),
                'type' => $request->input('matter'),
                'sub_type' => $request->input('sub_type'),
                'status' => 0,
            ]);

            $oobID = null; // Default to null

            if ($userRole == 3) { // Local Secretary
                $oob = LocalOoB::where('local_council_meeting_id', $meetingID)->first();
                $oobID = $oob ? $oob->id : null;
            } elseif ($userRole == 4) { // University Secretary
                $oob = UniversityOoB::where('university_council_meeting_id', $meetingID)->first();
                $oobID = $oob ? $oob->id : null;
            } elseif ($userRole == 5) { // Board Secretary
                $oob = BoardOoB::where('bor_meeting_id', $meetingID)->first();
                $oobID = $oob ? $oob->id : null;
            }



            // Attach proposal to the corresponding meeting agenda
            $this->attachProposalToAgenda($meetingID, $proposal->id, $userRole, $oobID);

            // Handle file uploads
            $fileIds = [];
            $file_order_no = 1;
            if ($request->hasFile('proposal_files')) {
                foreach ($request->file('proposal_files') as $file) {
                    $originalNameWithExt = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                    // Extract filename without final extension
                    preg_match('/^(.*?)(\(\d+\))?(\.\w+)?$/', $originalNameWithExt, $matches);
                    $baseName = trim($matches[1]);
                    $filename = "{$baseName}.{$extension}";

                    $i = 1;
                    while (Storage::disk('public')->exists("proposals/{$filename}")) {
                        $filename = "{$baseName} ({$i}).{$extension}";
                        $i++;
                    }

                    // Store the file
                    $filePath = $file->storeAs('proposals', $filename, 'public');

                    // Save file record in DB
                    $proposalFile = ProposalFile::create([
                        'proposal_id' => $proposal->id,
                        'file' => $filename,
                        'version' => 1,
                        'file_status' => 1,
                        'file_reference_id' => null,
                        'is_active' => true,
                        'order_no' => $file_order_no,
                    ]);

                    $fileIds[] = $proposalFile->id;
                    $file_order_no++;
                }
            }

            // Save file IDs in proposal logs
            $fileIdsString = implode(',', $fileIds);

            ProposalLog::create([
                'proposal_id' => $proposal->id,
                'employee_id' => session('employee_id'),
                'comments' => null,
                'status' => 0,
                'level' => 0,
                'action' => 7,
                'file_id' => $fileIdsString,
            ]);

            $proponentIds = explode(',', $request->input('proponent_email'));

            foreach ($proponentIds as $employeeId) {
                $proponent = Employee::where('EmailAddress', $request->input('proponent_email'))->first();

                ProposalProponent::create([
                    'proposal_id' => $proposal->id,
                    'employee_id' => $proponent->id,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('toastr', [
                'type' => 'success',
                'message' => 'Proposal added successfully!',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title' => "Something went wrong!"]);
        }
    }

    /**
     * Get the correct meeting model based on ID and role.
     */
    private function getMeetingModel($meetingID, $roleID)
    {
        if ($roleID == 3 && LocalCouncilMeeting::where('id', $meetingID)->exists()) {
            return LocalCouncilMeeting::find($meetingID);
        } elseif ($roleID == 4 && UniversityCouncilMeeting::where('id', $meetingID)->exists()) {
            return UniversityCouncilMeeting::find($meetingID);
        } elseif ($roleID == 5 && BorMeeting::where('id', $meetingID)->exists()) {
            return BorMeeting::find($meetingID);
        }
        return null;
    }

    /**
     * Attach proposal to the appropriate agenda table based on the meeting type.
     */
    private function attachProposalToAgenda($meetingID, $proposalID, $roleID, $oobID = null)
    {
        $status = 0; // Default status

        if ($roleID == 3) { // Local Secretary
            $existsInOOB = LocalOoB::where('local_council_meeting_id', $meetingID)->exists();
            LocalMeetingAgenda::create([
                'local_council_meeting_id' => $meetingID,
                'local_proposal_id' => $proposalID,
                'local_oob_id' => $oobID, // Store OOB ID
                'status' => $existsInOOB ? 1 : 0, // Update status
            ]);
        } elseif ($roleID == 4) { // University Secretary
            $existsInOOB = UniversityOoB::where('university_council_meeting_id', $meetingID)->exists();
            UniversityMeetingAgenda::create([
                'university_meeting_id' => $meetingID,
                'university_proposal_id' => $proposalID,
                'university_oob_id' => $oobID, // Store OOB ID
                'status' => $existsInOOB ? 1 : 0, // Update status
            ]);
        } elseif ($roleID == 5) { // Board Secretary
            $existsInOOB = BoardOoB::where('bor_meeting_id', $meetingID)->exists();
            BoardMeetingAgenda::create([
                'bor_meeting_id' => $meetingID,
                'bor_proposal_id' => $proposalID,
                'board_oob_id' => $oobID, // Store OOB ID
                'status' => $existsInOOB ? 1 : 0, // Update status
            ]);
        }
    }

    //ADD OTHER MATTER
    public function addOtherMatters(Request $request, String $meeting_id)
    {
        DB::beginTransaction();
        try {
            $proponent_email = is_array($request->proponent_email) ? $request->proponent_email[0] : $request->proponent_email;
            $request->merge(['proponent_email' => $proponent_email]);


            $meetingID = decrypt($meeting_id);
            $userRole = session('user_role');

            // Ensure the user has the correct role (Secretary: Local=3, University=4, Board=5)
            if (!in_array($userRole, [3, 4, 5])) {
                return response()->json([
                    'type' => 'danger',
                    'message' => 'You are not authorized to submit other matters!',
                    'title' => "Unauthorized"
                ]);
            }

            // Retrieve meeting model based on role
            $meeting = $this->getMeetingModel($meetingID, $userRole);
            if (!$meeting) {
                return response()->json([
                    'type' => 'danger',
                    'message' => 'Invalid meeting ID.',
                    'title' => "Error"
                ]);
            }

            // Validate input
            $request->validate([
                'proponent_email' => 'required|email|exists:employees,EmailAddress',
                'title' => 'required|string|max:255',
                'action' => 'required|string|max:255',
                'proposal_files' => 'required|array',
                'proposal_files.*' => 'file|mimes:pdf,xls,xlsx,csv|max:100000',
                'matter' => 'required|integer',
                'sub_type' => 'nullable|integer',
            ]);

            $proponent = Employee::where('EmailAddress', $request->input('proponent_email'))->first();

            $campus_id = session('campus_id');

            // Create Proposal Entry
            $proposal = Proposal::create([
                'employee_id' => $proponent->id,
                'campus_id' => $campus_id,
                'title' => $request->input('title'),
                'action' => $request->input('action'),
                'type' => $request->input('matter'),
                'sub_type' => $request->input('sub_type'),
                'status' => 0,
            ]);

            $oobID = null; // Default to null


            if ($userRole == 3) { // Local Secretary
                $oob = LocalOoB::where('local_council_meeting_id', $meetingID)->first();
                $oobID = $oob ? $oob->id : null;
            } elseif ($userRole == 4) { // University Secretary
                $oob = UniversityOoB::where('university_council_meeting_id', $meetingID)->first();
                $oobID = $oob ? $oob->id : null;
            } elseif ($userRole == 5) { // Board Secretary
                $oob = BoardOoB::where('bor_meeting_id', $meetingID)->first();
                $oobID = $oob ? $oob->id : null;
            }

            // Attach the proposal to the appropriate meeting agenda
        $this->attachProposalToAgenda($meetingID, $proposal->id, $userRole, $oobID);

        // Attach the proposal to the Other Matters table
        $this->attachProposalToOtherMatter($proposal->id, $userRole);

            // Handle File Uploads
            $fileIds = [];
            $file_order_no = 1;
            if ($request->hasFile('proposal_files')) {
                foreach ($request->file('proposal_files') as $file) {
                    $originalNameWithExt = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                    // Extract filename without final extension
                    preg_match('/^(.*?)(\(\d+\))?(\.\w+)?$/', $originalNameWithExt, $matches);
                    $baseName = trim($matches[1]);
                    $filename = "{$baseName}.{$extension}";

                    $i = 1;
                    while (Storage::disk('public')->exists("proposals/{$filename}")) {
                        $filename = "{$baseName} ({$i}).{$extension}";
                        $i++;
                    }

                    // Store the file
                    $filePath = $file->storeAs('proposals', $filename, 'public');

                    // Save file record in DB
                    $proposalFile = ProposalFile::create([
                        'proposal_id' => $proposal->id,
                        'file' => $filename,
                        'version' => 1,
                        'file_status' => 1,
                        'file_reference_id' => null,
                        'is_active' => true,
                        'order_no' => $file_order_no,
                    ]);

                    $fileIds[] = $proposalFile->id;
                    $file_order_no++;
                }
            }

            // Save file IDs in proposal logs
            $fileIdsString = implode(',', $fileIds);

            ProposalLog::create([
                'proposal_id' => $proposal->id,
                'employee_id' => session('employee_id'),
                'comments' => null,
                'status' => 0,
                'level' => 0,
                'action' => 7,
                'file_id' => $fileIdsString,
            ]);

            $proponentIds = explode(',', $request->input('proponent_email'));

            foreach ($proponentIds as $employeeId) {
                $proponent = Employee::where('EmailAddress', $request->input('proponent_email'))->first();

                ProposalProponent::create([
                    'proposal_id' => $proposal->id,
                    'employee_id' => $proponent->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Other Matter added successfully!',
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


    private function attachProposalToOtherMatter($proposalID, $roleID)
    {
        if ($roleID == 3) {
            OtherMatter::create([
                'proposal_id' => $proposalID,
            ]);
        } elseif ($roleID == 4) {
            OtherMatter::create([
                'proposal_id' => $proposalID,
            ]);
        } elseif ($roleID == 5) {
            OtherMatter::create([
                'proposal_id' => $proposalID,
            ]);
        }
    }






    // RENAME FILE
    public function renameFile(Request $request)
    {
        try{
            $request->validate([
                'file_id' => 'required|exists:proposal_files,id',
                'new_file_name' => 'required|string|max:255'
            ]);


            $file = ProposalFile::findOrFail($request->file_id);
            $oldPath = "proposals/{$file->file}";

            // Extract extension
            $extension = pathinfo($file->file, PATHINFO_EXTENSION);
            $newFileName = "{$request->new_file_name}.{$extension}";
            $newPath = "proposals/{$newFileName}";

            // Check if the new file name already exists
            if (Storage::disk('public')->exists($newPath)) {
                return response()->json(['message' => 'File name already exists!'], 400);
            }

            // Rename file in storage
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->move($oldPath, $newPath);
            }

            // Update the database record
            $file->file = $newFileName;
            $file->save();

            return response()->json(['type'=>'success','title'=>'Success!','message' => 'File renamed successfully!']);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
        }
    }

    // CHANGE PROPOSAL FILE ORDER
    public function updateOrder(Request $request)
    {
        try{
            $files = $request->input('files');

            foreach ($files as $file) {

                ProposalFile::where('id', $file['id'])
                    ->update(['order_no' => $file['order_no']]);
            }

            return response()->json(['message' => 'File order updated successfully']);
        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }


    // VIEW  MY PROPOSALS
    public function viewMyProposals(Request $request) {
        $proposals = Proposal::with(['proponents', 'files']) // Eager load related models
            ->whereHas('proponents', function ($query) {
                $query->where('proposal_proponents.employee_id', session('employee_id'));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $employeeId = session('employee_id');
        $proposalCounts = Proposal::proposalsCountByEmployeeInLevel($employeeId);



        return view('content.proposals.myProposals', compact('proposals', 'proposalCounts'));
    }



    // VIEW PROPOSAL DETAILS
    public function viewProposalDetails(Request $request, String $proposal_id)
    {
        $proposalID = decrypt($proposal_id);

        $proposal = Proposal::with(['proponents'])->findOrFail($proposalID);

        $proposal_logs = ProposalLog::where('proposal_id', $proposalID)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('content.proposals.viewProposalDetails', compact('proposal', 'proposal_logs'));
    }


    // VIEW EDIT PROPOSAL
    public function viewEditProposal(Request $request, String $proposal_id){
        $proposalID = decrypt($proposal_id);

        $proposal = Proposal::with(['proponents'])->where('id', $proposalID)->first();

        $proposal_logs = ProposalLog::where('proposal_id', $proposalID)
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->get();


        return view('content.proposals.editProposal', compact('proposal', 'proposal_logs'));
    }

    // NEW EDIT PROPONENT IN PROPONENT
    // NEWEST EDIT
    public function editProposal(Request $request, String $proposal_id)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'action' => 'required|string|max:255',
                'proponents' => 'required',
                'matter' => 'required|integer',
                'sub_type' => 'nullable|integer',
                'proposal_files' => 'nullable|array',
                'proposal_files.*' => 'file|mimes:pdf,xls,xlsx,csv|max:100000',
                'reuploaded_files' => 'nullable|array',
                'reuploaded_files.*' => 'file|mimes:pdf,xls,xlsx,csv|max:100000',
                'deleted_files' => 'nullable|array',
            ]);

            $proposal = Proposal::findOrFail(decrypt($proposal_id));

            $status = $proposal->status;
            $new_status = session('isProponent') && in_array($status, [2, 5, 6]) ? 9 : $status;

            // Update Proposal Details
            $proposal->update([
                'title' => $validated['title'],
                'action' => $validated['action'],
                'type' => $validated['matter'],
                'sub_type' => $validated['sub_type'] ?? null,
                'status' => $new_status,
            ]);

            // Update Meeting Agenda Status
            switch ($proposal->getCurrentLevelAttribute()) {
                case 0:
                    LocalMeetingAgenda::where('local_proposal_id', decrypt($proposal_id))->update(['status' => $new_status]);
                    break;
                case 1:
                    UniversityMeetingAgenda::where('university_proposal_id', decrypt($proposal_id))->update(['status' => $new_status]);
                    break;
                case 2:
                    BoardMeetingAgenda::where('board_proposal_id', decrypt($proposal_id))->update(['status' => $new_status]);
                    break;
            }

            $proponentIds = explode(',', $validated['proponents']);

            // Get existing proponents for the proposal
            $existingProponents = ProposalProponent::where('proposal_id', $proposal->id)
            ->pluck('employee_id')
            ->toArray();

            // Determine which proponents to add and which to remove
            $proponentsToAdd = array_diff($proponentIds, $existingProponents);
            $proponentsToRemove = array_diff($existingProponents, $proponentIds);

            // Remove proponents that are not in the validated array
            if (!empty($proponentsToRemove)) {
                ProposalProponent::where('proposal_id', $proposal->id)
                    ->whereIn('employee_id', $proponentsToRemove)
                    ->delete();
            }

            // Add new proponents that are not already in the table
            foreach ($proponentsToAdd as $employee_id) {
                ProposalProponent::create([
                    'proposal_id' => $proposal->id,
                    'employee_id' => $employee_id,
                ]);
            }


            $fileStatus = 1;
            $reuploadedFileStatus = 4;

            if(session('isSecretary')){
                $fileStatus = 2;
                $reuploadedFileStatus = 2;
            }
            // Handle New Attachments
            $file_order_no = ProposalFile::where('proposal_id', $proposal->id)->where('is_active', true)->max('order_no') ?? 1;
            if ($request->hasFile('proposal_files')) {
                foreach ($request->file('proposal_files') as $file) {
                    $filename = $this->generateUniqueFilename($file);
                    $file->storeAs('proposals', $filename, 'public');

                    ProposalFile::create([
                        'proposal_id' => $proposal->id,
                        'file' => $filename,
                        'version' => 1,
                        'file_status' => $fileStatus,
                        'is_active' => true,
                        'order_no' => ++$file_order_no,
                    ]);
                }
            }

            // Handle Reuploaded Files
            if ($request->hasFile('reuploaded_files')) {
                foreach ($request->file('reuploaded_files') as $fileId => $file) {
                    $filename = $this->generateUniqueFilename($file);
                    $file->storeAs('proposals', $filename, 'public');

                    $oldFile = ProposalFile::find($fileId);
                    if ($oldFile) {
                        $oldFile->update(['is_active' => false]);

                        ProposalFile::create([
                            'proposal_id' => $proposal->id,
                            'file' => $filename,
                            'version' => $oldFile->version + 1,
                            'file_status' => $reuploadedFileStatus,
                            'file_reference_id' => $fileId,
                            'is_active' => true,
                            'order_no' => $oldFile->order_no,
                        ]);
                    }
                }
            }

            // Handle Deleted Files
            if (!empty($validated['deleted_files'])) {
                ProposalFile::whereIn('id', $validated['deleted_files'])->delete();
            }

            // Log Proposal Update for Proponents
            if (session('isProponent') && in_array($status, [2, 5, 6])) {
                ProposalLog::create([
                    'proposal_id' => $proposal->id,
                    'employee_id' => session('employee_id'),
                    'status' => $new_status,
                    'level' => $proposal->getCurrentLevelAttribute(),
                    'action' => 8,
                ]);
            }

            DB::commit();

            return response()->json(['type' => 'success', 'message' => 'Proposal updated successfully!', 'title' => 'Success!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title' => 'Something went wrong!']);
        }
    }

    // NEW DELETE PROPOSAL
    public function deleteProposal(Request $request)
    {
        try {
            $request->validate([
                'proposal_id' => 'required',
            ]);

            $proposal_id = decrypt($request->input('proposal_id'));
            $proposal = Proposal::findOrFail($proposal_id);

            // Delete related records in agenda tables
            LocalMeetingAgenda::where('local_proposal_id', $proposal_id)->delete();
            UniversityMeetingAgenda::where('university_proposal_id', $proposal_id)->delete();
            BoardMeetingAgenda::where('board_proposal_id', $proposal_id)->delete();

            // Delete the proposal
            $proposal->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'Proposal and related agendas deleted successfully!',
                'title' => 'Success!',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }


    // DELETE PROPOSAL FILE

    public function deleteFile(Request $request)
    {
        try{
            $request->validate([
                'file_id' => 'required|exists:proposal_files,id'
            ]);

            $proposalFile = ProposalFile::find($request->file_id);
            $proposalFile->delete();

            return response()->json(['type' => 'success', 'message' => 'File deleted successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
        }
    }

    // VIEW MEETINGS WITH NUMBER FOR PROPOSALS
    public function viewMeetingsWithProposalCount(Request $request){
        $role = session('user_role');
        $employeeId = session('employee_id');
        $campus_id = session('campus_id');
        $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));

        if($role == 3 && $level == 0){
            $meetings = LocalCouncilMeeting::where('campus_id', $campus_id)
            ->withCount('proposals')
            ->orderBy('created_at', 'desc')
            ->get();
        }

        if($role == 4 && $level == 1){
            $meetings = UniversityCouncilMeeting::withCount('proposals')
            ->orderBy('created_at', 'desc')
            ->get();
        }

        if($role == 5 && $level == 2){
            $meetings = BorMeeting::withCount('proposals')
            ->orderBy('created_at', 'desc')
            ->get();
        }

        return view('content.proposals.viewProposals', compact('meetings'));
    }

    // VIEW PROPOSAL IN SPECIFIC MEETING
    public function viewMeetingProposals($level, $meeting_id)
    {
        $meetingID = decrypt($meeting_id);
        $proposals = collect(); // Initialize as an empty collection
        $meeting = null; // Initialize the meeting variable

        if ($level == 'Local') {
            $meeting = LocalCouncilMeeting::find($meetingID);
            $proposals = LocalMeetingAgenda::where("local_council_meeting_id", $meetingID)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($level == 'University') {
            $meeting = UniversityCouncilMeeting::find($meetingID);
            $proposals = UniversityMeetingAgenda::where("university_meeting_id", $meetingID)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($level == 'BOR') {
            $meeting = BorMeeting::find($meetingID);
            $proposals = BoardMeetingAgenda::where("bor_meeting_id", $meetingID)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('content.proposals.viewMeetingProposal', compact('proposals', 'meeting'));
    }

    // VIEW PROPSOSAL DEATILS (SECRETRARY POV)
    public function viewProposalDetails_Secretary(Request $request, String $proposal_id)
    {
        $proposalID = decrypt($proposal_id);
        $proposal = Proposal::with(['proponents'])->where('id', $proposalID)->first();

        $meeting = $proposal->meeting;

        $proposal_logs = ProposalLog::where('proposal_id', $proposalID)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();


        // dd($proposal);

        return view('content.proposals.viewProposal', compact('proposal', 'proposal_logs', 'meeting'));
    }


    // UPDATE SELECTED PROPOSALS STATUS
    public function updateSelectedProposalStatus(Request $request)
    {
        DB::beginTransaction();

        try {
            $level = session('secretary_level');
            $status = $request->input('action') + 1;

            $request->validate([
                'proposals' => 'required|array',
                'action' => 'required|integer'
            ]);

            $decryptedIds = collect($request->proposals)->map(callback: function ($id) {
                try {
                    $decrypted = decrypt($id);
                    return is_numeric($decrypted) ? (int) $decrypted : null; // Ensure it's a valid integer
                } catch (\Exception $e) {
                    return null; // Skip invalid IDs
                }
            })->filter(); // Remove null values

            // Ensure at least one valid ID exists before updating
            if ($decryptedIds->isEmpty()) {
                return response()->json(['type' => 'danger', 'message' => 'Invalid Proposal IDs', 'title' => "Something went wrong!"]);
            }

            // Update status in the Proposal table
            Proposal::whereIn('id', $decryptedIds)->update(['status' => $status]);

            foreach ($decryptedIds as $proposal_id) {
                $proposal = Proposal::find($proposal_id);

                if (!$proposal) continue; // Skip if proposal is not found

                // Determine the meeting level and update the respective agenda table
                if ($proposal->getCurrentLevelAttribute() == 0) {
                    LocalMeetingAgenda::where('local_proposal_id', $proposal_id)->update(['status' => $status]);
                } elseif ($proposal->getCurrentLevelAttribute() == 1) {
                    UniversityMeetingAgenda::where('university_proposal_id', $proposal_id)->update(['status' => $status]);
                } elseif ($proposal->getCurrentLevelAttribute() == 2) {
                    BoardMeetingAgenda::where('board_proposal_id', $proposal_id)->update(['status' => $status]);
                }

                // Create a log entry for each updated proposal
                ProposalLog::create([
                    'proposal_id' => $proposal_id,
                    'employee_id' => session('employee_id'),
                    'status' => $status,
                    'comments' => NULL,
                    'level' => $level,
                    'action' => $request->input('action'),
                    'file_id' => "",
                ]);

                ProposalFile::where('proposal_id', $proposal_id)
                ->where('is_active', true)
                ->update([
                    'proposal_id' => $proposal_id,
                    'file_status' => 2,
                ]);
            }

            DB::commit();
            return response()->json(['type' => 'success', 'message' => 'Status updated successfully', 'title' => 'Success']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title' => "Something went wrong!"]);
        }
    }

    // UPDATE SPECIFIC PROPOSAL STATUS - SECRETARY POV
    public function updateProposalStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $level =session('secretary_level');
            $status = $request->input('action') + 1;

            $request->validate([
                'proposal_id' => 'required|string',
                'action' => 'required|integer'
            ]);

            if (in_array($request->input('action'), [1, 4, 5, 6])) {
                $request->validate([
                    'comment' => 'required'
                ]);
            }

            $file_ids = "";
            if (in_array($request->input('action'), [1, 4, 5])) {
                $request->validate([
                    'proposal_files' => 'required|array'
                ]);

                $file_ids = implode(",", $request->input('proposal_files'));
            }

            $proposal_id = decrypt($request->input('proposal_id'));

            // Get the current proposal status
            $proposal = Proposal::find($proposal_id);
            if (!$proposal) {
                return response()->json(['type' => 'danger', 'message' => 'Proposal not found.', 'title' => 'Error']);
            }

            if ($proposal->status == $request->input('action') + 1) {
                return response()->json([
                    'type' => 'info',
                    'message' => 'The proposal is already in this status.',
                    'title' => 'No Change'
                ]);
            }

            // Update the proposal status
            $proposal->update(['status' => $status]);

            if ($proposal->getCurrentLevelAttribute() == 0) {
            LocalMeetingAgenda::where('local_proposal_id', $proposal_id)->update(['status' => $status]);
            } elseif ($proposal->getCurrentLevelAttribute() == 1) {
            UniversityMeetingAgenda::where('university_proposal_id', $proposal_id)->update(['status' => $status]);
            } elseif ($proposal->getCurrentLevelAttribute() == 2) {
            BoardMeetingAgenda::where('board_proposal_id', $proposal_id)->update(['status' => $status]);
            }

            // Log the status update
            ProposalLog::create([
                'proposal_id' => $proposal_id,
                'employee_id' => session('employee_id'),
                'status' => $request->input('action') + 1,
                'comments' => $request->input('comment'),
                'level' => $level,
                'action' => $request->input('action'),
                'file_id' => $file_ids,
            ]);

            if (in_array($request->input('action'), [1, 4, 5])) {
                foreach ($request->input('proposal_files') as $file_id) {
                    ProposalFile::where('id', $file_id)
                        ->update([
                            'proposal_id' => $proposal_id,
                            'file_status' => 3,
                        ]);
                }
            }else{
            ProposalFile::where('proposal_id', $proposal_id)
                        ->where('is_active', true)
                        ->update([
                            'proposal_id' => $proposal_id,
                            'file_status' => 2,
                        ]);
            }

            DB::commit();
            return response()->json([
                'type' => 'success',
                'message' => 'Status updated successfully',
                'title' => 'Success'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => 'Something went wrong!'
            ]);
        }
    }


    // VIEW SUBMIT PROPOSAL - SECRETARY POV
    public function viewSubmitProposalSecretary(Request $request, String $level, String $meeting_id)
    {
        try {
            $proposals = collect();
            $matters = [0 => 'Financial Matters'] + config('proposals.matters');
            $meeting = null;
            $campus_id =  session('campus_id');

            $meetingID = decrypt($meeting_id);

            if($level == 'University'){
                $meeting = UniversityCouncilMeeting::find($meetingID);
            }
            if($level == 'BOR'){
                $meeting = BorMeeting::find($meetingID);
            }

            if ($level == 'University') {  // LOCAL PROPOSALS THAT WILL BE SUBMITTED TO UNIVERSITY
                $proposals = LocalMeetingAgenda::with('proposal')
                // ->where('status', 4)
                ->whereHas('proposal', function ($query): void  {
                    $query->where('status', 4);
                })
                ->whereHas('meeting', function ($query) use ($campus_id) {
                    $query->where('campus_id', $campus_id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            } elseif ($level == 'BOR') { // UNIVERSITY PROPOSALS THAT WILL BE SUBMITTED TO BOR
                $proposals = UniversityMeetingAgenda::with('proposal')
                    // ->where('status', 4)
                    ->whereHas('proposal', function ($query)  {
                        $query->where('status', 4);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            // dd($proposals);
        
            // Get meeting type
            $councilType = $meeting->council_type ?? null;
            $councilTypesConfig = config('proposals.council_types');

            // Initialize categorized proposals
            $categorizedProposals = [];
            foreach ($matters as $type => $title) {
                $categorizedProposals[$type] = collect();
            }

            // Categorize proposals
            foreach ($proposals as $proposal) {
                $type = $proposal->proposal->type;
                $subType = $proposal->proposal->sub_type;

                if (!isset($categorizedProposals[$type])) {
                    $categorizedProposals[$type] = collect();
                }

                // Separate Financial Matters from Administrative Matters
                if ($type == 2 && $subType == 0) {
                    $type = 0;
                }

                $categorizedProposals[$type][] = $proposal;
            }

            // dd($categorizedProposals);
            return view('content.proposals.secretarySubmitProposals', compact(
                'meeting',
                'categorizedProposals',
                'matters',
            ));

        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }

    // SUBMIT PROPOSALS - SECRETRARY POV
    public function submitProposalSecretary(Request $request, String $level, String $meeting_id){
        DB::beginTransaction();
        try{
            $proposal_level = 0;
            $status = 0;
            $campus_id =  session('campus_id');

            $meetingID = decrypt($meeting_id);

            if($level == 'University'){
                $meeting = UniversityCouncilMeeting::find($meetingID);
            }
            if($level == 'BOR'){
                $meeting = BorMeeting::find($meetingID);
            }

            if ($meeting->getIsSubmissionClosedAttribute() || ($meeting->status == 1)){
                return response()->json(['type' => 'danger','message' => 'The meeting is already closed!', 'title'=> "Meeting Closed!"]);
            } else{
                $request->validate([
                'endorsedProposals' => 'required',
            ]);

            $proposalIDs = $request->input('endorsedProposals');

            if ($level == 'University') {
                $proposal_level = 1;
                $status = 8;

                foreach($proposalIDs as $proposal_ID){
                    Proposal::where('id', $proposal_ID)->update([
                        'status' => $status,
                        ]);

                    UniversityMeetingAgenda::create([
                        'university_proposal_id' => $proposal_ID,
                        'university_meeting_id' => $meeting ->id,
                        'status' => $status,
                    ]);

                    ProposalLog::create([
                        'proposal_id' => $proposal_ID,
                        'employee_id' => session('employee_id'),
                        'status' => $status,
                        'level' => $proposal_level,
                        'action' => 7,
                    ]);
                }

            } elseif ($level == 'BOR') {
                $proposal_level = 2;
                $status = 8;

                foreach($proposalIDs as $proposal_ID){
                    Proposal::where('id', $proposal_ID)->update([
                        'status' => $status,
                        ]);

                    BoardMeetingAgenda::create([
                        'board_proposal_id' => $proposal_ID,
                        'bor_meeting_id' => $meeting->id,
                        'status' => $status,
                    ]);


                    ProposalLog::create([
                        'proposal_id' => $proposal_ID,
                        'employee_id' => session('employee_id'),
                        'status' => $status,
                        'level' => $proposal_level,
                        'action' => 7,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'type' => 'success',
                'message' => 'Proposals submitted successfully!',
                'title'=> "Success!",
                'redirect' => route(getUserRole(). ".meetings")
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
        }
    }

    /**
     * Generate a unique filename for uploaded files.
     */
    private function generateUniqueFilename($file)
    {
        $originalNameWithExt = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        preg_match('/^(.*?)(\(\d+\))?(\.\w+)?$/', $originalNameWithExt, $matches);
        $baseName = trim($matches[1]);
        $filename = "{$baseName}.{$extension}";

        $i = 1;
        while (Storage::disk('public')->exists("proposals/{$filename}")) {
            $filename = "{$baseName} ({$i}).{$extension}";
            $i++;
        }

        return $filename;
    }
}
