<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Meetings;
use App\Models\Venues;
use App\Models\Comments;
use App\Models\Employee;
use App\Models\User;
use App\Models\HrmisEmployee;

use App\Models\OrderOfBusiness;
use App\Models\Proposal_Logs;
use App\Models\Proposal_Files;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;


use Illuminate\Support\Facades\Mail;
use App\Mail\SubmitProposalMail;
use App\Mail\ReturnProposalMail;
use App\Mail\DeferredProposalMail;
use App\Mail\ProposalStatusUpdateMail;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class ProposalController extends Controller
{
  // NEW PROPOSAL CONTROLLER CODE
  public function viewProposalDetails(Request $request, String $proposal_id)
  {
    $proposalID = decrypt($proposal_id);

    $proposal = Proposal::where('id', $proposalID)->first();

    $proponentIds = explode(',', $proposal->proponent_id);
    $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();

    $user = Auth::user();
    $role = $user->role;
    $meeting_id_level = $role == 3 ? 'local_meeting_id' : ($role == 4 ? 'university_meeting_id' : ($role == 5 ? 'board_meeting_id' : 'local_meeting_id'));

    $meeting_id = Proposal::where('id', $proposalID)->value($meeting_id_level );


    $meeting = Meetings::where('id', $meeting_id)->first();

    // Fetch proposal logs and order by latest updates first
    $proposal_logs = Proposal_Logs::where('proposal_id', $proposalID)
        ->orderBy('created_at', 'asc')
        ->get();

    // dd($meeting);
    return view('content.proposals.viewProposalDetails', compact('proposal', 'meeting', 'proposal_logs'));
  }

  // VIEW PROPSOSAL DEATILS (SECRETRARY POV)
  public function viewProposalDetails_Secretary(Request $request, String $proposal_id){
    $proposalID = decrypt($proposal_id);

    $proposal = Proposal::where('id', $proposalID)->first();

    $proponentIds = explode(',', $proposal->proponent_id);
    $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();

    $user = Auth::user();
    $role = $user->role;
    $meeting_id_level = $role == 3 ? 'local_meeting_id' : ($role == 4 ? 'university_meeting_id' : ($role == 5 ? 'board_meeting_id' : 'local_meeting_id'));

    $meeting_id = Proposal::where('id', $proposalID)->value($meeting_id_level );


    $meeting = Meetings::where('id', $meeting_id)->first();

    // Fetch proposal logs and order by latest updates first
    $proposal_logs = Proposal_Logs::where('proposal_id', $proposalID)
    ->with('user') // Eager load user details
    ->orderBy('created_at', 'asc')
    ->get();
    

    return view('content.proposals.viewProposal', compact('proposal', 'meeting', 'proposal_logs'));
  }  
  // public function viewEditProposal(Request $request, String $proposal_id){
  //   $proposalID = decrypt($proposal_id);
  //   $user = Auth::user();
  //   $role = $user->role;
  //   $meeting_id_level = $role == 3 ? 'local_meeting_id' : ($role == 4 ? 'university_meeting_id' : ($role == 5 ? 'board_meeting_id' : 'local_meeting_id'));

  //   $proposal = Proposal::where('id', $proposalID)->first();

  //   $proponentIds = explode(',', string: $proposal->proponent_id);
  //   $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();

  //   $meeting_id = $proposal?-> $meeting_id_level;

  //   $meeting = Meetings::where('id', $meeting_id)->first();

  //   // dd($meeting);
  //   return view('content.proposals.editProposal', compact('proposal', 'meeting'));
  // }

  public function viewEditProposal(Request $request, String $proposal_id){
    $proposalID = decrypt($proposal_id);

    $proposal = Proposal::where('id', $proposalID)->first();

    $proponentIds = explode(',', $proposal->proponent_id);
    $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();

    $user = Auth::user();
    $role = $user->role;
    // $meeting_id_level = $role == 3 ? 'local_meeting_id' : ($role == 4 ? 'university_meeting_id' : ($role == 5 ? 'board_meeting_id' : 'local_meeting_id'));

    // $meeting_id = Proposal::where('id', $proposalID)->value($meeting_id_level );


    // $meeting = Meetings::where('id', $meeting_id)->first();

    $meeting = Meetings::where('id', $proposal->local_meeting_id)->first();

    if ($meeting) {
        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date_time); 
        $submissionEndDate = \Carbon\Carbon::parse($meeting->submission_end);

        $proposal->is_edit_disabled = now()->greaterThan($meetingDate) || now()->greaterThan($submissionEndDate);
    } else {
        $proposal->is_edit_disabled = false; 
    }
    // dd($proposal->is_edit_disabled);

    // Fetch proposal logs and order by latest updates first
    $proposal_logs = Proposal_Logs::where('proposal_id', $proposalID)
    ->with('user') // Eager load user details
    ->orderBy('created_at', 'asc')
    ->get();


    return view('content.proposals.editProposal', compact('proposal', 'meeting', 'proposal_logs'));

  }


  public function viewSubmitProposal(Request $request, String $meeting_id)
  {
    $meeting_id = decrypt($meeting_id);
    $meeting = Meetings::where('id', $meeting_id)->first();
    // $meeting = Meetings::getMeetingsWithVenue($meeting_id);
    // dd($meeting);
    return view('content.proposals.submitProposal', compact('meeting'));
  }

  public function searchUsers(Request $request)
  {
      $query = trim($request->input('query'));
  
      $campus_id = Employee::where('id', Auth::user()->employee_id)->value('campus');
  
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


  public function submitProposal(Request $request, String $meeting_id)
  {
    try{
      $meeting = Meetings::where('id', decrypt($meeting_id))->first();

      $currentDate = now();
      $submissionEnd = \Carbon\Carbon::parse($meeting->submission_end);
      $isSubmissionClosed = $currentDate->greaterThan($submissionEnd);

      if ($isSubmissionClosed || ($meeting->status == 1)){
        return response()->json(['type' => 'danger','message' => 'The meeting is already closed!', 'title'=> "Meeting Closed!"]);
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
        $campus_id = Employee::where('id', Auth::user()->employee_id)->value('campus');

        $proposal = Proposal::create([
          'proponent_id' => $request->input('proponents'),
          'campus_id' => $campus_id,
          'title' => $request->input('title'),
          'action' => $request->input('action'),
          // 'file' => $request->input('proposalFiles'),
          'local_meeting_id' => decrypt($meeting_id),
          'type' => $request->input('matter'),
          'sub_type' => $request->input('sub_type'),
        ]);

        $files = explode('/', $request->input('proposalFiles'));

        $fileIds = []; 

        foreach ($files as $file) {
            $proposalFile = Proposal_Files::create([
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
        
        $proposal_logs = Proposal_Logs::create([
            'proposal_id' => $proposal?->id,
            'user_id' => auth()->user()->id,
            'comments' => null,
            'status' => 0,
            'level' => 0,
            'action' => 7,
            'file_id' => $fileIdsString, 
        ]);
        

        $submitter = Auth::user();
        $creatorCampusId = $meeting->campus_id;
        
        $creators_id = User::find($meeting->creator_id);
        $meetingCreator = Employee::find($creators_id->employee_id);
        $creatorEmail = $meetingCreator?->EmailAddress;
        
        $recipientEmails = [];
        $cellphoneNumbers = [];
        
        // Get Local Secretaries' Emails
        if ($meeting->level == 0) {
            $localSecretaries = DB::table('secretaries')
                ->where('position', 'Local Secretary')
                ->join('employee', 'secretaries.employee_id', '=', 'employee.id')
                ->where('employee.campus', $creatorCampusId)
                ->select('employee.EmailAddress', 'employee.Cellphone')
                ->get();
        
            foreach ($localSecretaries as $secretary) {
                if (!empty($secretary->EmailAddress)) {
                    $recipientEmails[] = $secretary->EmailAddress;
                }
                if (!empty($secretary->Cellphone)) {
                    $cellphoneNumbers[$secretary->EmailAddress] = $secretary->Cellphone;
                }
            }
        }
        
        // Ensure the meeting creator gets the email
        if ($creatorEmail && !in_array($creatorEmail, $recipientEmails)) {
            $recipientEmails[] = $creatorEmail;
        }
        
        // Remove duplicate emails
        $recipientEmails = array_unique($recipientEmails);
        
        // Send email notification
        Mail::to($recipientEmails)->send(new SubmitProposalMail($meeting, $submitter->name));
        
        // Send SMS
        foreach ($recipientEmails as $email) {
            $cellphone = $cellphoneNumbers[$email] ?? null;
        
            if (empty($cellphone)) {
                // Fetch Cellphone from HRMIS if missing in Employee
                $hrmisEmployee = HrmisEmployee::where('EmailAddress', $email)->first();
                if ($hrmisEmployee && !empty($hrmisEmployee->Cellphone)) {
                    $cellphone = $hrmisEmployee->Cellphone;
                }
            }
        
            // Send SMS if cellphone exists
            if (!empty($cellphone)) {
              $title = $request->input('title') ?? 'a proposal';

      
              $message = "Advisory!\nA new proposal titled \"$title\" has been submitted by $submitter->name. Please review it at policy.southernleytestateu.edu.ph.";
              

                $smsController = new SMSController();
                $smsController->send($cellphone, $message);
            }
        }


        return response()->json(['type' => 'success','message' => 'Proposal submitted successfully!', 'title'=> "Success!"]);
      }
    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }

  public function updateOrder(Request $request)
{
  if (!in_array(auth()->user()->role_id, [3, 4, 5])) {
    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
}

  foreach ($request->order as $proposal) {
    Proposal::where('id', $proposal['id'])->update(['order' => $proposal['order']]);
}

return response()->json(['success' => true]);

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
    try {

      $proponent_email = is_array($request->proponent_email) ? $request->proponent_email[0] : $request->proponent_email;
      $request->merge(['proponent_email' => $proponent_email]);

        $meeting = Meetings::where('id', decrypt($meeting_id))->first();
      

        $request->validate([
            'proponent_email' => 'required|email|exists:users,email',
            'title' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'proposalFiles' => 'required|string',
            'matter' => 'required|integer',
            'sub_type' => 'nullable|integer',
        ]);

        $proponent = User::where('email', $request->input('proponent_email'))->first();
        $campus_id = Employee::where('id', $proponent->employee_id)->value('campus');
        // Determine level and meeting ID based on role
        $level = 0;
        $meetingColumn = 'local_meeting_id';
        $oobColumn = 'order_of_business_id';
        $status = 0;

        if (auth()->user()->role == 4) {
            $level = 1;
            $meetingColumn = 'university_meeting_id';
            $oobColumn = 'university_oob_id';

        }

        if (auth()->user()->role == 5) {
          $level = 2;
          $meetingColumn = 'board_meeting_id';
          $oobColumn = 'board_oob_id';

      }

        $orderOfBusiness = OrderOfBusiness::where('meeting_id', decrypt($meeting_id))->first();
        $oob_id = $orderOfBusiness ? $orderOfBusiness->id : null;
        if($oob_id != null){
          $status = 1;
        }
        $proposal = Proposal::create([
            'proponent_id' => $proponent->id,
            'campus_id' => $campus_id,
            'title' => $request->input('title'),
            'action' => $request->input('action'),
            'type' => $request->input('matter'),
            'sub_type' => $request->input('sub_type'),
            'level' => $level,
            'status' =>$status,
            $meetingColumn => decrypt($meeting_id),
            $oobColumn => $oob_id,
        ]);

        $files = explode('/', $request->input('proposalFiles'));
        $fileIds = [];

        foreach ($files as $file) {
            $proposalFile = Proposal_Files::create([
                'proposal_id' => $proposal->id,
                'file' => trim($file),
                'version' => 1,
                'file_status' => 1,
                'file_reference_id' => null,
                'level' => $level,
                'is_active' => true,
            ]);

            $fileIds[] = $proposalFile->id;
        }

        $fileIdsString = implode(',', $fileIds);

        Proposal_Logs::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->user()->id,
            'comments' => null,
            'level' => $level,
            'action' => 7,
            'status' =>$status,

            'file_id' => $fileIdsString,
        ]);

        return redirect()->back()->with('toastr', [
          'type' => 'success',
          'message' => 'Proposal added successfully!',
      ]);    

    } catch (\Throwable $th) {
        return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title' => "Something went wrong!"]);
    }
}


  // NEW EDIT PROPONENT IN PROPONENT
  public function editProposal(Request $request, String $proposal_id)
  {
    try{
      $request->validate([
        'title' => 'required|string|max:255',
        'action' => 'required|string|max:255',
        'proposalFiles' => 'nullable|string',
        'proponents' => 'required',
        'matter' => 'required|integer',
        'sub_type' => 'nullable|integer',
      ]);

      $status = Proposal::where('id', decrypt($proposal_id))->value('status');
      $new_status = $status;
      $proposal = Proposal::find(decrypt($proposal_id));

      if (!$proposal) {
          return response()->json(['type' => 'danger', 'message' => 'Proposal not found.', 'title' => 'Something went wrong!']);
      }
      
      if(in_array($status, [2,5,6])){
        $new_status = 9;
      }

      // Update the proposal
      $proposal->update([
          'proponent_id' => $request->input('proponents'),
          'title' => $request->input('title'),
          'action' => $request->input('action'),
          'type' => $request->input('matter'),
          'sub_type' => $request->input('sub_type'),
          'status' => $new_status,
      ]);

      $files = explode('/', $request->input('proposalFiles'));

      $fileIds = [];
      $fileIdsString = "";
      
      if (!empty($files) && $files !== [""]) {
          foreach ($files as $file) {
              $trimmedFile = trim($file);
              
              if ($trimmedFile === "") {
                  continue;              }
      
              $proposalFile = Proposal_Files::create([
                  'proposal_id' => $proposal->id,
                  'file' => $trimmedFile,
                  'version' => 1,
                  'file_status' => 1,
                  'file_reference_id' => null,
                  'level' => $proposal->level, 
                  'is_active' => true,
              ]);
      
              $fileIds[] = $proposalFile->id;
          }
      
          if (!empty($fileIds)) {
              $fileIdsString = implode(',', $fileIds);
          }
      }
      
      if(in_array($status, [2,5,6])){
        $proposal_logs = Proposal_Logs::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->user()->id,
            'comments' => null,
            'status' => $new_status,
            'level' => $proposal->level,
            'action' => 8,
            'file_id' => $fileIdsString, 
        ]);
      }
      
  
      return response()->json(['type' => 'success','message' => 'Proposal updated successfully!', 'title'=> "Success!"]);
    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }
  // public function editProposal(Request $request, String $proposal_id)
  // {
  //   try{
  //     $request->validate([
  //       'title' => 'required|string|max:255',
  //       'action' => 'required|string|max:255',
  //       'proposalFiles' => 'required|string',
  //       'proponents' => 'required',
  //       'matter' => 'required|integer',
  //       'sub_type' => 'nullable|integer',
  //     ]);

  //     $status = Proposal::where('id', decrypt($proposal_id))->value('status');

  //     // IF THE CURRENT STATUS IS RETURNED
  //     if($status == 1){
  //       $status = 0;
  //       Proposal_Logs::create([
  //         'proposal_id' => decrypt($proposal_id),
  //         'status' =>  $status,
  //         'remarks' => '',
  //         'level' => 0,
  //       ]);
  //     }

  //     $proposal = Proposal::where('id', decrypt($proposal_id))->update([
  //       'proponent_id' => $request->input('proponents'),
  //       'title' => $request->input('title'),
  //       'action' => $request->input('action'),
  //       'file' => $request->input('proposalFiles'),
  //       'type' => $request->input('matter'),
  //       'sub_type' => $request->input('sub_type'),
  //       'status' => $status,
  //     ]);

  //     return response()->json(['type' => 'success','message' => 'Proposal updated successfully!', 'title'=> "Success!"]);
  //   } catch (\Throwable $th) {
  //     return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
  //   }
  // }


  public function updateSelectedProposalStatus(Request $request)
  {
    try{
      $level = 0;
      if(auth()->user()->role == 3){
        $level = 0;
      }else if(auth()->user()->role == 4){
        $level = 1;
      }else if(auth()->user()->role == 5){
        $level = 2;
      }
      $status = $request->input('action') + 1;

      $request->validate([
        'proposals' => 'required|array',
        'action' => 'required|integer'
      ]);

      $decryptedIds = collect($request->proposals)->map(function ($id) {
        try {
            $decrypted = decrypt($id);
            return is_numeric($decrypted) ? (int) $decrypted : null; // Ensure it’s a valid integer
        } catch (\Exception $e) {
            return null; // Skip invalid IDs
        }
      })->filter(); // Remove null values
      
      // Ensure that at least one valid ID exists before updating
      if ($decryptedIds->isEmpty()) {
          return response()->json(['type' => 'danger', 'message' => 'Invalid Proposal IDs', 'title' => "Something went wrong!"]);
      }
      
      Proposal::whereIn('id', $decryptedIds)->update(['status' => $status]);


      $selectedPropsals = $decryptedIds;

      foreach($selectedPropsals as $proposal_id){
        Proposal_Logs::create([
          'proposal_id' => $proposal_id,
          'user_id' => auth()->user()->id,
          'status' => $status,
          'comments' => '',
          'level' => $level,
          'action' => $request->input('action'),
          'file_id' => "",
        ]);
      }

      $statusMap = config('proposals.status', []);
      $statusText = $statusMap[$status] ?? 'Unknown Status';

      // Fetch all proponents along with their proposals
      $proponents = User::whereIn('id', function ($query) use ($selectedPropsals) {
          $query->select('proponent_id')->from('proposals')->whereIn('id', $selectedPropsals);
      })->get();

      // Fetch proposals with their titles
      $proposals = Proposal::whereIn('id', $selectedPropsals)->get();

      // Group proposals by proponent
      $proponentProposals = [];
      foreach ($proposals as $proposal) {
          $proponentProposals[$proposal->proponent_id][] = $proposal->title;
      }

      // Send emails to all unique proponents
      foreach ($proponents as $proponent) {
          $proposalTitles = $proponentProposals[$proponent->id] ?? [];

          Mail::to($proponent->email)->send(
            new ProposalStatusUpdateMail($statusText, $proponent->name, $proposalTitles)
        );
      }

    return response()->json(['type' => 'success', 'message' => 'Status updated successfully', 'title' => 'Success']);
    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }

  public function updateProposalStatus(Request $request)
  {
    try {
        $level = match (auth()->user()->role) {
            3 => 0,
            4 => 1,
            5 => 2,
            default => 0,
        };

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
        $proposal->update(['status' => $request->input('action') + 1]);

        // Log the status update
        Proposal_Logs::create([
            'proposal_id' => $proposal_id,
            'user_id' => auth()->user()->id,
            'status' => $request->input('action') + 1,
            'comments' => $request->input('comment'),
            'level' => $level,
            'action' => $request->input('action'),
            'file_id' => $file_ids,
        ]);

        // Update file statuses if applicable
        if (in_array($request->input('action'), [1, 4, 5])) {
            foreach ($request->input('proposal_files') as $file_id) {
                Proposal_Files::where('id', $file_id)
                    ->update([
                        'proposal_id' => $proposal_id,
                        'file_status' => 3,
                        'level' => $level,
                    ]);
            }
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
  public function editProposalSecretary(Request $request, String $proposal_id)
  {
    try{
      $proposal_id = decrypt($proposal_id);
      $request->validate([
        'title' => 'required|string|max:255',
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
        'title' => $request->input('title'),
        'type' => $request->input('matter'),
        'action' => $request->input('action'),
        'sub_type' => $sub_type,
      ]);

      return response()->json(['type' => 'success', 'message' => 'Proposal updated successfully', 'title' => 'Success']);    
    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }

  public function returnProposal(Request $request){
    try{
      $request->validate([
        'proposal_id' => 'required',
        'remarks' => 'required'
      ]);

      $level = 0;
      if(auth()->user()->role == 3){
        $level = 0;
      }else if(auth()->user()->role == 4){
        $level = 1;
      }else if(auth()->user()->role == 5){
        $level = 2;
      }

      $proposal_id = $request->input('proposal_id');


      Proposal_Logs::create([
        'proposal_id' => $proposal_id,
        'status' => 1,
        'remarks' => $request->input('remarks'),
        'level' => $level,
      ]);

      Proposal::where('id',  $proposal_id)->update([
        'status' => 1,
      ]);

        $remarks = $request->input('remarks');
        // Fetch the proposal
        $proposal = Proposal::findOrFail($proposal_id);
        // Fetch the proponent's email from the User table using proposal_id
        $proponent = User::where('id', $proposal->proponent_id)->first();
        // Send email to the proponent
        Mail::to($proponent->email)->send(new ReturnProposalMail($proposal, $remarks, $proponent->name));


      return response()->json(['type' => 'success', 'message' => 'Proposal returned successfully', 'title' => 'Success']);

    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Error!"]);
    }
  }

  public function deferredProposal(Request $request){
    try{
      $request->validate([
        'proposal_id' => 'required',
        'remarks' => 'required'
      ]);

      $level = 0;
      if(auth()->user()->role == 3){
        $level = 0;
      }else if(auth()->user()->role == 4){
        $level = 1;
      }else if(auth()->user()->role == 5){
        $level = 2;
      }

      $proposal_id = $request->input('proposal_id');


      Proposal_Logs::create([
        'proposal_id' => $proposal_id,
        'status' => 3,
        'remarks' => $request->input('remarks'),
        'level' => $level,
      ]);

      Proposal::where('id',  $proposal_id)->update([
        'status' => 3,
      ]);



      $remarks = $request->input('remarks');
      // Fetch the proposal
      $proposal = Proposal::findOrFail($proposal_id);
      // Fetch the proponent's email from the User table using proposal_id
      $proponent = User::where('id', $proposal->proponent_id)->first();
      // Send email to the proponent
      Mail::to($proponent->email)->send(new DeferredProposalMail($proposal, $remarks, $proponent->name));


      return response()->json(['type' => 'success', 'message' => 'Proposal deferred successfully', 'title' => 'Success']);

    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Error!"]);
    }
  }

  // public function storeMedia(Request $request)
  // {
  //   $file = $request->file('file');

  //   $newFileName = $file->getClientOriginalName();

  //   // Store in storage/proposals but make it accessible from public/storage/proposals
  //   $filePath = $file->storeAs('proposals', $newFileName, 'public');

  //   return response()->json([
  //       'name' => $newFileName,
  //       'original_name' => $file->getClientOriginalName(),
  //       'path' => asset('storage/proposals/' . $newFileName) // Publicly accessible URL
  //   ]);
  // }

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



  public function viewMyProposals(Request $request){
    $proposals = Proposal::where('proponent_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $meetings = Meetings::get();

    foreach ($proposals as $proposal) {
        $proponentIds = explode(',', $proposal->proponent_id);
        $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();

        $proposal->files = Proposal_Files::where('proposal_id', $proposal->id)->get();

        $meeting = Meetings::where('id', $proposal->local_meeting_id)->first();

        if ($meeting) {
            $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date_time); 
            $submissionEndDate = \Carbon\Carbon::parse($meeting->submission_end);

            $proposal->is_edit_disabled = now()->greaterThan($meetingDate) || now()->greaterThan($submissionEndDate);
        } else {
            $proposal->is_edit_disabled = false; 
        }
    }
    // dd($proposal->is_edit_disabled);
    return view('content.proposals.myProposals', compact('proposals', 'meetings'));
  }


  public function viewMeetingProposals($meeting_id)
  {
      $meetingID = decrypt($meeting_id);
      $user = Auth::user();
      $role = $user->role;
      $meeting = Meetings::findOrFail($meetingID);
      
      $meeting_id_level = $role == 3 ? 'local_meeting_id' : ($role == 4 ? 'university_meeting_id' : ($role == 5 ? 'board_meeting_id' : 'local_meeting_id'));

      if (in_array(auth()->user()->role, [0,1,2])) {
          $proposals = Proposal::where('local_meeting_id', $meetingID)
                              ->where('proponent_id', auth()->user()->id)
                              ->paginate(10);
      } else {
          $proposals = Proposal::where($meeting_id_level, $meetingID)
                              ->paginate(10);
      }

      foreach ($proposals as $proposal) {
          // Fetch proponent details
          $proponentIds = explode(',', $proposal->proponent_id);
          $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();

          // Fetch associated proposal files
          $proposal->files = Proposal_Files::where('proposal_id', $proposal->id)->get();

          if(in_array(auth()->user()->role, [0,1,2,6])){
            $meeting_prop = Meetings::where('id', $proposal->local_meeting_id)->first();

            if ($meeting_prop) {
                $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date_time); 
                $submissionEndDate = \Carbon\Carbon::parse($meeting->submission_end);
        
                $proposal->is_edit_disabled = now()->greaterThan($meetingDate) || now()->greaterThan($submissionEndDate);
            } else {
                $proposal->is_edit_disabled = false; 
            }
          }
      }

      return view('content.proposals.viewMeetingProposal', compact('proposals', 'meeting'));
  }


  // public function viewMeetingProposals($meeting_id)
  // {
  //   $meetingID = decrypt($meeting_id);
  //   $user = Auth::user();
  //   $role = $user->role;
  //   $meeting = Meetings::findOrFail($meetingID);
  //   $meeting_id_level = $role == 3 ? 'local_meeting_id' : ($role == 4 ? 'university_meeting_id' : ($role == 5 ? 'board_meeting_id' : 'local_meeting_id'));


  //   if(in_array(auth()->user()->role, [0,1,2])){
  //     $proposals = Proposal::where('local_meeting_id', $meetingID)->where('proponent_id', auth()->user()->id)->paginate(5);
  //   }else{
  //     $proposals = Proposal::where($meeting_id_level, $meetingID)->paginate(5);
  //   }


  //   foreach ($proposals as $proposal) {
  //       $proponentIds = explode(',', $proposal->proponent_id);
  //       $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();
  //   }

  //   // dd(

  //   // dd($meeting);
  //   return view('content.proposals.viewMeetingProposal', compact('proposals' , 'meeting'));
  // }
  


  // SECRERATRY SUBMIT PROPOSAL
  public function viewSubmitProposalSecretary(Request $request, String $meeting_id){
    try{
      $venue = "";
      $user = Auth::user();
      $role = $user->role;
      $status = 4;
      $level = $role == 3 ? 0 : ($role == 4 ?  1: 2);

      

      // dd($status);

      $meetingID = decrypt($meeting_id);
      $meeting = Meetings::where('id', $meetingID)->first();
      if($meeting->venue){
        $venue = Venues::where('id', $meeting->venue)->value('description');
      }

      $campus_id = Employee::where('id', Auth::user()->employee_id)->value('campus');

      $quarter = $meeting->quarter;
      $year    = $meeting->year;

      // Academic proposals: type 1 and status 5, with meeting matching campus, quarter, year.
      $academicProposals = Proposal::where('type', 1)
          ->where('status',   $status)
          ->where('level', $level)
          ->whereHas('meeting', function ($query) use ($campus_id, $quarter, $year) {
              $query->where('campus_id', $campus_id)
                    ->where('quarter', $quarter)
                    ->where('year', $year);
          })
          ->get();

      // Administrative proposals: type 2 and status 5, with meeting matching campus, quarter, year.
      $administrativeProposals = Proposal::where('type', 2)
          ->where('status',  $status)
          ->where('level', $level)
          ->whereHas('meeting', function ($query) use ($campus_id, $quarter, $year) {
              $query->where('campus_id', $campus_id)
                    ->where('quarter', $quarter)
                    ->where('year', $year);
          })
      ->get();



        foreach ([$academicProposals, $administrativeProposals] as $proposals) {
            foreach ($proposals as $proposal) {
                $proponentIds = explode(',', $proposal->proponent_id);
                $proposal->proponentsList = User::whereIn('id', $proponentIds)->get();
            }
        }


        return view('content.proposals.secretarySubmitProposals', compact(
            'meeting',
            'academicProposals',
            'administrativeProposals',
            'venue',
        ));
    } catch (\Throwable $th) {
        return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }

  public function submitProposalSecretary(Request $request, String $meeting_id){
    try{
      $meeting = Meetings::where('id', decrypt($meeting_id))->first();

      $currentDate = now();
      $submissionEnd = \Carbon\Carbon::parse($meeting->submission_end);
      $isSubmissionClosed = $currentDate->greaterThan($submissionEnd);

      if ($isSubmissionClosed || ($meeting->status == 1)){
        return response()->json(['type' => 'danger','message' => 'The meeting is already closed!', 'title'=> "Meeting Closed!"]);
      }
      else{
        $request->validate([
          'endorsedProposals' => 'required',
        ]);

        $campus_id = Employee::where('id', Auth::user()->employee_id)->value('campus');

        $proposalIDs = $request->input('endorsedProposals');
        $meeting_id_level = '';
        $level = 0;
        $status = 0;
        if(auth()->user()->role == 3){
          $level = 1;
          $status = 8;
          $meeting_id_level = 'university_meeting_id';
        }else if(auth()->user()->role == 4){
          $level = 2;
          $status = 8;
          $meeting_id_level = 'board_meeting_id';
        }else if(auth()->user()->role == 5){
          $level = 2;
        }

        foreach($proposalIDs as $proposal_ID){
          $proposal = Proposal::where('id', $proposal_ID)->update([
            'status' => $status,
            'level' => $level,
            $meeting_id_level => $meeting->id,
          ]);

          // $proposal_logs = Proposal_Logs::create([
          //   'proposal_id' => $proposal_ID,
          //   'status' => $status,
          //   'remarks' => '',
          //   'level' => $level,
          // ]);
          $proposal_logs = Proposal_Logs::create([
            'proposal_id' => $proposal_ID,
            'user_id' => auth()->user()->id,
            'comments' => null,
            'status' =>  $status,
            'level' => $level,
            'action' => 7,
          ]);
        }


          // Fetch submitter details
          $submitter = Auth::user();
          $creatorCampusId = $meeting->campus_id;

          // Get University Secretary for university meetings
          $recipientEmails = User::where('id', $meeting->creator_id)->get('email');

          // Send email notification
          Mail::to($recipientEmails)->send(new SubmitProposalMail($meeting, $submitter->name));



        return response()->json([
          'type' => 'success',
          'message' => 'Proposals submitted successfully!',
          'title'=> "Success!",
          'redirect' => route(getUserRole(). ".meetings")
          ]);
      }
    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }

  public function viewProposals(Request $request)
  {
    $user = Auth::user();
    $role = $user->role;
    $employeeId = $user->employee_id;
    $campus = Employee::where('id', $employeeId)->value('campus');
    $user_id = $user->id;

    $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));

    if (in_array($user->role, [0, 1, 2])) {
        $allowedCouncilTypes = [1];
        if ($role == 0) {
            $allowedCouncilTypes = [1, 2];
        } elseif ($role == 1) {
            $allowedCouncilTypes = [1, 3];
        } elseif ($role == 2) {
            $allowedCouncilTypes = [1, 2, 3];
        }
        $meetings = Meetings::where('campus_id', $campus)
        ->whereIn('council_type', $allowedCouncilTypes)
        ->where('level', 0)
        ->withCount(['proposals' => function ($query) use ($user_id) {
            $query->where('proponent_id', $user_id); // Count only the user's proposals
        }])
        ->get();
    }
    else if ($user->role == 3){
        // $meetings = Meetings::where('creator_id', $user_id)
          // ->where('campus_id', $campus)
          // ->where('level', $level)
          // ->withCount('proposals')
          // ->get();

        $meetings = Meetings::where('campus_id', $campus)
        ->where('level', $level)
        ->withCount('proposals')
        ->get();
        
    }else if(in_array($user->role, [4,5])){
        $meetings = Meetings::where('level', $level)
          ->withCount('proposals')
          ->get();
    }

    // dd($meetings);

    return view('content.proposals.viewProposals', compact('meetings'));
  }


  // NEWLY ADDED
  // Filter Meeting Proposals
  public function filterMeetingProposls(Request $request){
    $user = Auth::user(); 
    $role = $user->role;  
    $employeeId = $user->employee_id;
    $campus = Employee::where('id', $employeeId)->value('campus');
    $user_id = $user->id;
    
    $level = $role == 3 ? 0 : ($role == 4 ? 1 : ($role == 5 ? 2 : 0));

    $request->validate([
        'year' => 'required|string',
        'council_type' => 'required|integer',
    ]);

    if (in_array($user->role, [3,4,5])) {
        $filters = [
            'campus_id'    => $campus,
            'creator_id' => $user_id ,
            'year'         => $request->input('year'),
            'council_type' => $request->input('council_type'),
        ];
        
        $meetings = Meetings::where($filters)
        ->withCount('proposals')
        ->get();            
    }

    // dd($meetings);
    return response()->json([
        'type' => 'success',
        'html' => view('content.proposals.partials.meetings_proposal_table', compact('meetings'))->render()
    ]); 
  }

  // EDIT PROPOSAL FUNCTIONALITY
  public function reuploadFile(Request $request)
  {
    try{
      $request->validate([
          'file' => 'required|mimes:pdf,xls,xlsx,csv|max:100000',
          'file_id' => 'required|exists:proposal_files,id'
      ]);
  
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
  
      $filePath = $file->storeAs('proposals', $filename, 'public');
  
      // Update existing file record
      $proposalFile = Proposal_Files::find($request->file_id);
      $proposalFile->is_active = false;
      $proposalFile->save();
  
      $version = $proposalFile->version + 1;
      $proposal_id = $proposalFile->proposal_id;
      $level = $proposalFile->level;
  
      $newProposalFile = Proposal_Files::create([
          'proposal_id' => $proposal_id,
          'file' => $filename,
          'version' => $version,
          'file_status' => 4,
          'file_reference_id' => $request->file_id,
          'level' => $level,
          'is_active' => true,
      ]);
  
      return response()->json(['type' => 'success', 'message' => 'File reuploaded successfully.']);
    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }

  public function deleteFile(Request $request)
  {
    try{
      $request->validate([
          'file_id' => 'required|exists:proposal_files,id'
      ]);
  
      $proposalFile = Proposal_Files::find($request->file_id);
      $proposalFile->delete();

      return response()->json(['type' => 'success', 'message' => 'File deleted successfully.']);
    } catch (\Throwable $th) {
      return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
    }
  }
}
