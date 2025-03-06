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
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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
    
        if (!$campus_id) {
            return response()->json([]); 
        }
    
        $userRole = auth()->user()->role; 
        $roles = match ($userRole) {
            0 => [0, 2, 6],
            1 => [1, 2, 6],
            2 => [0, 1, 2, 6],
            default => [],
        };
    
        if (empty($roles)) {
            return response()->json([]);
        }
    
        // dd($roles);
        
        $users = User::whereIn('role', $roles)
            ->whereHas('employee', function ($q) use ($campus_id) {
                $q->where('campus', $campus_id);
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->get();
    
        return response()->json($users);
    }
    
  
    // SUBMIT PROPOSAL FOR PROPONENT (PROPONENT CAN ONLY SUBMIT IN LOCAL MEETING)
    public function submitProposal(Request $request, String $meeting_id)
    {
      try{
        
        $meetingID = decrypt($meeting_id);

        $meeting = LocalCouncilMeeting::find($meetingID);
  
        if ($meeting->getIsSubmissionClosedAttribute() || ($meeting->status == 1)){
          return response()->json(['type' => 'danger','message' => 'The submission or the meeting is already closed!', 'title'=> "Meeting Closed!"]);
        }
        else{
          $request->validate([
            'title' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'proposalFiles' => 'required|string',
            'proponents' => 'required',
            'matter' => 'required|integer',
            'sub_type' => 'nullable|integer',
          ]);

          $campus_id = session('campus_id');

          $proposal = Proposal::create([
            'employee_id' => $request->input('proponents'),
            'campus_id' => $campus_id,
            'title' => $request->input('title'),
            'action' => $request->input('action'),
            'type' => $request->input('matter'),
            'sub_type' => $request->input('sub_type'),
            'status' => 0,
          ]);
          
          LocalMeetingAgenda::create([
            'local_council_meeting_id' => $meetingID,
            'local_proposal_id' => $proposal?->id,
            'status' => 0,
          ]);
         
          $files = explode('/', $request->input('proposalFiles'));
  
          $fileIds = []; 
  
          foreach ($files as $file) {
              $proposalFile = ProposalFile::create([
                  'proposal_id' => $proposal?->id,
                  'file' => trim($file),
                  'version' => 1,
                  'file_status' => 1,
                  'file_reference_id' => null,
                  'is_active' => true,
              ]);
          
              $fileIds[] = $proposalFile->id;
          }
          
          $fileIdsString = implode(',', $fileIds);
          
          $proposal_logs = ProposalLog::create([
              'proposal_id' => $proposal?->id,
              'employee_id' => session('employee_id'),
              'comments' => null,
              'status' => 0,
              'level' => 0,
              'action' => 7,
              'file_id' => $fileIdsString, 
          ]);
          
          return response()->json(['type' => 'success','message' => 'Proposal submitted successfully!', 'title'=> "Success!"]);
        }
      } catch (\Throwable $th) {
        return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
      }
    }
    public function storeMedia(Request $request)
    {
      $file = $request->file('file');
      $originalNameWithExt = $file->getClientOriginalName();
      $extension = $file->getClientOriginalExtension();

      // Extract filename without the final extension
      preg_match('/^(.*?)(\(\d+\))?(\.\w+)?$/', $originalNameWithExt, $matches);
      $baseName = trim($matches[1]); // Filename without versioning
      $filename = "{$baseName}.{$extension}";

      $i = 1;
      while (Storage::disk('public')->exists("proposals/{$filename}")) {
          $filename = "{$baseName} ({$i}).{$extension}";
          $i++;
      }

      // Store in storage/proposals but make it accessible from public/storage/proposals
      $filePath = $file->storeAs('proposals', $filename, 'public');

      return response()->json([
          'name' => $filename,
      ]);
    }
    public function deleteMedia(Request $request)
    {
        $fileName = $request->input('filename');

        // Define the path in storage/app/public/proposals
        $filePath = storage_path('app/public/proposals/' . $fileName);

        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file from storage
            return response()->json(['message' => 'File deleted successfully']);
        }

        return response()->json(['message' => 'File not found'], 404);
    }


    // VIEW PROPOSALS
    public function viewMyProposals(Request $request){
      $proposals = Proposal::where('employee_id', session('employee_id'))
          ->orderBy('created_at', 'desc')
          ->get();
  
      foreach ($proposals as $proposal) {
          // Proposal's Proponents 
          $proponentIds = explode(',', $proposal->employee_id);
          $proposal->proponentsList = User::whereIn('employee_id', $proponentIds)->get();

          // Proposal Files
          $proposal->files = ProposalFile::where('proposal_id', $proposal->id)->get();
      }
      return view('content.proposals.myProposals', compact('proposals'));
    }

    // VIEW PROPOSAL DETAILS
    public function viewProposalDetails(Request $request, String $proposal_id)
  {
    $proposalID = decrypt($proposal_id);

    $proposal = Proposal::where('id', $proposalID)->first();

    $proponentIds = explode(',', $proposal->employee_id);
    $proposal->proponentsList = User::whereIn('employee_id', $proponentIds)->get();


    // Fetch proposal logs and order by latest updates first
    $proposal_logs = ProposalLog::where('proposal_id', $proposalID)
        ->orderBy('created_at', 'asc')
        ->get();

    // dd($meeting);
    return view('content.proposals.viewProposalDetails', compact('proposal', 'proposal_logs'));
  }

  // VIEW EDIT PROPOSAL
  public function viewEditProposal(Request $request, String $proposal_id){
    $proposalID = decrypt($proposal_id);

    $proposal = Proposal::where('id', $proposalID)->first();

    $proponentIds = explode(',', $proposal->employee_id);
    $proposal->proponentsList = User::whereIn('employee_id', $proponentIds)->get();


    // Fetch proposal logs and order by latest updates first
    $proposal_logs = ProposalLog::where('proposal_id', $proposalID)
    ->with('user') // Eager load user details
    ->orderBy('created_at', 'asc')
    ->get();


    return view('content.proposals.editProposal', compact('proposal', 'proposal_logs'));
  }

  // NEW EDIT PROPONENT IN PROPONENT
  // NEWEST EDIT
  public function editProposal(Request $request, String $proposal_id)
  {
    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'proponents' => 'required',
            'matter' => 'required|integer',
            'sub_type' => 'nullable|integer',
            'proposalFiles' => 'nullable|string',
            'reuploaded_files' => 'nullable|array',
            'reuploaded_files.*' => 'file|mimes:pdf,xls,xlsx,csv|max:100000',
            'deleted_files' => 'nullable|array',
        ]);

        $proposal = Proposal::find(decrypt($proposal_id));

        if (!$proposal) {
            return response()->json(['type' => 'danger', 'message' => 'Proposal not found.', 'title' => 'Something went wrong!']);
        }

        $status = $proposal->status;
        $new_status = in_array($status, [2, 5, 6]) ? 9 : $status;

        // Update the proposal details
        $proposal->update([
            'proponent_id' => $request->input('proponents'),
            'title' => $request->input('title'),
            'action' => $request->input('action'),
            'type' => $request->input('matter'),
            'sub_type' => $request->input('sub_type'),
            'status' => $new_status,
        ]);

        $fileIds = [];

        // Handle new attachments
        $newFiles = explode('/', $request->input('proposalFiles'));
        $newFileIds = [];
        $fileIdsString = "";
        
        if (!empty($newFiles) && $newFiles !== [""]) {
            foreach ($newFiles as $file) {
                $trimmedFile = trim($file);
                
                if ($trimmedFile === "") {
                    continue;              }
        
                $proposalFile = ProposalFile::create([
                    'proposal_id' => $proposal->id,
                    'file' => $trimmedFile,
                    'version' => 1,
                    'file_status' => 1,
                    'file_reference_id' => null,
                    'is_active' => true,
                ]);
        
                $newFileIds[] = $proposalFile->id;
            }
        
            if (!empty($newFileIds)) {
                $fileIdsString = implode(',', $newFileIds);
            }
        }

        // Handle reuploaded files
        if ($request->hasFile('reuploaded_files')) { 
          foreach ($request->file('reuploaded_files') as $fileId => $file) {
              $originalNameWithExt = $file->getClientOriginalName();
              $extension = $file->getClientOriginalExtension();
      
              // Extract filename without versioning
              preg_match('/^(.*?)(\(\d+\))?(\.\w+)?$/', $originalNameWithExt, $matches);
              $baseName = trim($matches[1]); 
              $filename = "{$baseName}.{$extension}";
      
              // Ensure unique filename
              $i = 1;
              while (Storage::disk('public')->exists("proposals/{$filename}")) {
                  $filename = "{$baseName} ({$i}).{$extension}";
                  $i++;
              }
      
              $filePath = $file->storeAs('proposals', $filename, 'public');
      
              // Find the existing file using $fileId
              $proposalFile = ProposalFile::find($fileId);
              if (!$proposalFile) {
                  continue; // Skip if file record is not found
              }
      
              // Deactivate the old file
              $proposalFile->is_active = false;
              $proposalFile->save();
      
              // Create a new version
              $newProposalFile = ProposalFile::create([
                  'proposal_id' => $proposalFile->proposal_id,
                  'file' => $filename,
                  'version' => $proposalFile->version + 1,
                  'file_status' => 4,
                  'file_reference_id' => $fileId, 
                  'is_active' => true,
              ]);
          }
      }
      

        // Handle file deletions
        if ($request->input('deleted_files')) {
          foreach($request->input('deleted_files') as $fileID)
          {
            ProposalFile::where('id', $fileID)->delete();
          }
           
        }

        // Log if proposal status changed
        if (in_array($status, [2, 5, 6])) {
            ProposalLog::create([
                'proposal_id' => $proposal->id,
                'employee_id' => auth()->user()->id,
                'comments' => null,
                'status' => $new_status,
                'level' => $proposal->getCurrentLevelAttribute(),
                'action' => 8,
                'file_id' => implode(',', $fileIds), 
            ]);
        }

        return response()->json(['type' => 'success', 'message' => 'Proposal updated successfully!', 'title' => 'Success!']);
    } catch (\Throwable $th) {
        return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title' => 'Something went wrong!']);
    }
  }

  // NEW DELETE PROPOSAL CODE 
  public function deleteProposal(Request $request){
    try{
      $request->validate([
        'proposal_id' => 'required',
      ]);
      $proposal_id = $request->input('proposal_id');
      Proposal::find(decrypt($proposal_id))->delete();
      
      return response()->json(['type' => 'success', 'message' => 'Proposal deleted successfully!', 'title' => 'Success!']);

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
        ->get();
    }
   
    if($role == 4 && $level == 1){
        $meetings = UniversityCouncilMeeting::withCount('proposals')->get();
    }

    if($role == 5 && $level == 2){
        $meetings = BorMeeting::withCount('proposals')->get();
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

    if ($proposals->isNotEmpty()) {
        foreach ($proposals as $proposal) {
            // Proposal's Proponents
            $proponentIds = explode(',', $proposal->proposal->employee_id);
            // dd($proponentIds);
            $proposal->proponentsList = User::whereIn('employee_id', $proponentIds)->get();

            // Proposal Files
            $proposal->files = ProposalFile::where('proposal_id', $proposal->id)->get();
        }
    }

    return view('content.proposals.viewMeetingProposal', compact('proposals', 'meeting'));
  }
  // VIEW PROPSOSAL DEATILS (SECRETRARY POV)
  public function viewProposalDetails_Secretary(Request $request, String $proposal_id)
  {
    $proposalID = decrypt($proposal_id);
    $proposal = Proposal::where('id', $proposalID)->first();

    $meeting = $proposal->meeting;

    $proponentIds = explode(',', $proposal->employee_id);
    $proposal->proponentsList = User::whereIn('employee_id', $proponentIds)->get();

    // Fetch proposal logs and order by latest updates first
    $proposal_logs = ProposalLog::where('proposal_id', $proposalID)
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->get();

  
    // dd($meeting);

    return view('content.proposals.viewProposal', compact('proposal', 'proposal_logs', 'meeting'));
  }


  // UPDATE SELECTED PROPOSALS STATUS
  public function updateSelectedProposalStatus(Request $request)
  {
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
                'comments' => '',
                'level' => $level,
                'action' => $request->input('action'),
                'file_id' => "",
            ]);
        }

        return response()->json(['type' => 'success', 'message' => 'Status updated successfully', 'title' => 'Success']);

    } catch (\Throwable $th) {
        return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title' => "Something went wrong!"]);
    }
  }

  // UPDATE SPECIFIC PROPOSAL STATUS - SECRETARY POV
  public function updateProposalStatus(Request $request)
  {
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
            'employee_id' => auth()->user()->id,
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
        
        return response()->json([
            'type' => 'success',
            'message' => 'Status updated successfully',
            'title' => 'Success'
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'type' => 'danger',
            'message' => $th->getMessage(),
            'title' => 'Something went wrong!'
        ]);
    }
  }
  
  // UPDATE PROPOSAL - SECRETARY POV
  public function editProposalSecretary(Request $request, String $proposal_id)
  {
    try{
      $proposal_id = decrypt($proposal_id);
      $request->validate([
        'matter' => 'required|integer',
        'action' => 'required|integer'
      ]);
    
      $matter = $request->input('matter');

      $sub_type = null;
      if($matter == 2){
        $request->validate([
          'sub_type' => 'required|integer',
        ]);
        $sub_type = $request->input('sub_type');
      }

      $proposal = Proposal::where('id', $proposal_id)
      ->update([
        'type' => $request->input('matter'),
        'action' => $request->input('action'),
        'sub_type' => $sub_type,
      ]);

      return response()->json(['type' => 'success', 'message' => 'Proposal updated successfully', 'title' => 'Success']);    
    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }

}
