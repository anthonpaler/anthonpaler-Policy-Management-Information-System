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
                  'level' => 0,
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
          $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();

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
    $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();


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
    $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();


    // Fetch proposal logs and order by latest updates first
    $proposal_logs = ProposalLog::where('proposal_id', $proposalID)
    ->with('user') // Eager load user details
    ->orderBy('created_at', 'asc')
    ->get();


    return view('content.proposals.editProposal', compact('proposal', 'proposal_logs'));

  }

}
