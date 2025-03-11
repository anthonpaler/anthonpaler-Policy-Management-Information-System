<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\LocalCouncilMeeting;
use App\Models\UniversityCouncilMeeting;
use App\Models\BorMeeting;
use App\Models\LocalMeetingAgenda;
use App\Models\UniversityMeetingAgenda;
use App\Models\BoardMeetingAgenda;
use App\Models\User;
use App\Models\ProposalFile;
use App\Models\LocalOob;
use App\Models\UniversityOob;
use App\Models\BoardOob;
use App\Models\Venues;


class OrderOfBusinessController extends Controller
{
    // VIEW GENERATE OOB
    public function viewGenerateOOB(Request $request, String $level, String $meeting_id)
    {
        try {
            $meetingID = decrypt($meeting_id);
            $proposals = collect();
            $matters = config('proposals.matters');
            $meeting = null;

            if ($level == 'Local') {
                $meeting = LocalCouncilMeeting::find($meetingID);
                $proposals = LocalMeetingAgenda::where("local_council_meeting_id", $meetingID)
                    ->with('proposal')
                    ->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($level == 'University') {
                $meeting = UniversityCouncilMeeting::find($meetingID);
                $proposals = UniversityMeetingAgenda::where("university_meeting_id", $meetingID)
                    ->with('proposal')
                    ->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($level == 'BOR') {
                $meeting = BorMeeting::find($meetingID);
                $proposals = BoardMeetingAgenda::where("bor_meeting_id", $meetingID)
                    ->with('proposal')
                    ->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            // Get meeting type
            $councilType = $meeting->council_type ?? null;
            $councilTypesConfig = config('proposals.council_types');

            $categorizedProposals = [];

            foreach ($matters as $type => $title) {
                $categorizedProposals[$type] = $proposals->filter(fn($p) => $p->proposal->type === $type) ?? collect();
            }

            if (!isset($categorizedProposals[$type])) {
                $categorizedProposals[$type] = collect(); 
            }
        
            foreach ($categorizedProposals as &$proposalsGroup) {
                foreach ($proposalsGroup as $proposal) {
                    $proponentIds = explode(',', $proposal->proposal->employee_id);
                    $proposal->proponentsList = User::whereIn('employee_id', $proponentIds)->get();
                    $proposal->files = ProposalFile::where('proposal_id', $proposal->proposal->id)->get();
                }
            }
            // dd($categorizedProposals);
            return view('content.orderOfBusiness.generateOOB', compact('meeting', 'categorizedProposals', 'matters'));

        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }

    // GENERATE OOB
    public function generateOOB(Request $request, String $level, String $meeting_id)
    {
        try {
            $request->validate([
                'preliminaries' => 'required|string',
            ]);

            $meetingID = decrypt($meeting_id);

            // Determine the correct model dynamically
            $oobModel = match ($level) {
                'Local' => LocalOob::class,
                'University' => UniversityOob::class,
                'BOR' => BoardOob::class,
                default => null
            };

            if (!$oobModel) {
                return response()->json([
                    'type' => 'danger',
                    'message' => 'Invalid meeting level!',
                    'title' => "Error!"
                ]);
            }

            // Check if an Order of Business already exists
            if ($oobModel::where($this->getMeetingColumn($level), $meetingID)->exists()) {
                return response()->json([
                    'type' => 'info',
                    'message' => 'This meeting already has an Order of Business!',
                    'title' => "Duplicate Entry"
                ]);
            }

            // Save the OOB to the correct table
            $oobModel::create([
                $this->getMeetingColumn($level) => $meetingID,
                'preliminaries' => $request->input('preliminaries'),
                'status' => 0,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Order of Business generated successfully!',
                'title' => "Success!"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }

    // View OOB List
    public function viewOOBList(Request $request)
    {

        $campus_id = session('campus_id');
        $oobLevel = match (session('user_role')) {
            4 => 'University',
            5 => 'BOR',
            default => 'Local'
        };

        $oobModel = match ($oobLevel) {
            'Local' => LocalOob::class,
            'University' => UniversityOob::class,
            'BOR' => BoardOob::class,
            default => null
        };

        if (!$oobModel) {
            return abort(404, 'Invalid Order of Business Level');
        }

        if(session('isProponent')){
            $orderOfBusiness = $oobModel::with('meeting')
            ->whereHas('meeting', function ($query) use ($campus_id) { 
                $query->where('campus_id', $campus_id);
            })
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();
        
        } else{
            if(session('user_role') == 3){
                $orderOfBusiness = $oobModel::with('meeting' )
                ->whereHas('meeting', function ($query) use ($campus_id) { 
                    $query->where('campus_id', $campus_id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            }else{
                $orderOfBusiness = $oobModel::with('meeting' )
                ->orderBy('created_at', 'desc')
                ->get();
            }
          
        }
        // dd($orderOfBusiness);
        return view('content.orderOfBusiness.viewOOBList', compact('orderOfBusiness'));
    }

    // VIEW OOB
    public function viewOOB(Request $request, String $level, String $oob_id)
    {
        try {
            $oobID = decrypt($oob_id);
            $proposals = collect();
            $matters = config('proposals.matters');
            $orderOfBusiness = null;
            $meeting = null;

            if ($level == 'Local') {
                $orderOfBusiness = LocalOob::with('meeting')->findOrFail($oobID);
            } elseif ($level == 'University') {
                $orderOfBusiness = UniversityOob::with('meeting')->findOrFail($oobID);
            } elseif ($level == 'BOR') {
                $orderOfBusiness = BoardOob::with('meeting')->findOrFail($oobID);
            } else {
                throw new \Exception("Invalid council level provided.");
            }

            $meeting = $orderOfBusiness->meeting;

            if (!$meeting) {
                throw new \Exception("Meeting not found for the given Order of Business.");
            }

            if ($level == 'Local') {
                $proposals = LocalMeetingAgenda::where("local_council_meeting_id", $meeting->id)
                    ->with('proposal')
                    ->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($level == 'University') {
                $proposals = UniversityMeetingAgenda::where("university_meeting_id", $meeting->id)
                    ->with('proposal')
                    ->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($level == 'BOR') {
                $proposals = BoardMeetingAgenda::where("bor_meeting_id", $meeting->id)
                    ->with('proposal')
                    ->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            // Get meeting type
            $councilType = $meeting->council_type ?? null;
            $councilTypesConfig = config('proposals.council_types');

            $categorizedProposals = [];

            foreach ($matters as $type => $title) {
                $categorizedProposals[$type] = $proposals->filter(fn($p) => $p->proposal->type === $type) ?? collect();
            }

            if (!isset($categorizedProposals[$type])) {
                $categorizedProposals[$type] = collect();
            }

            foreach ($categorizedProposals as &$proposalsGroup) {
                foreach ($proposalsGroup as $proposal) {
                    $proponentIds = explode(',', $proposal->proposal->employee_id);
                    $proposal->proponentsList = User::whereIn('employee_id', $proponentIds)->get();
                    $proposal->files = ProposalFile::where('proposal_id', $proposal->proposal->id)->get();
                }
            }

            // dd($categorizedProposals, $orderOfBusiness, $meeting);

            return view('content.orderOfBusiness.viewOOB', compact(
                'orderOfBusiness',
                'meeting',
                'categorizedProposals',
                'matters'
            ));

        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }

    public function disseminateOOB(Request $request, String $level,String $oob_id){
        try{
            $oobID = decrypt($oob_id);

            if ($level == 'Local') {
                $orderOfBusiness = LocalOob::with('meeting')->findOrFail($oobID);
            } elseif ($level == 'University') {
                $orderOfBusiness = UniversityOob::with('meeting')->findOrFail($oobID);
            } elseif ($level == 'BOR') {
                $orderOfBusiness = BoardOob::with('meeting')->findOrFail($oobID);
            } else {
                throw new \Exception("Invalid council level provided.");
            }
            if(!$orderOfBusiness->meeting->meeting_date_time || !$orderOfBusiness->meeting->modality){
                return response()->json([
                    'type' => 'warning',
                    'message' => 'Make sure that there  is aleady a meeting date and modality!',
                    'title' => "Warning!"
                ]);
            }
            $request->validate([
                'endorsedProposalIds' => 'required|array|min:1'
            ]);

            $endorsedProposalIds = $request->input('endorsedProposalIds');

            foreach ($endorsedProposalIds as $proposal_id) {
                if ($level == 'Local') {
                    LocalMeetingAgenda::where('local_proposal_id', $proposal_id)
                       ->update(['local_oob_id' => $oobID]);
                } elseif ($level == 'University') {
                    UniversityMeetingAgenda::where('university_proposal_id', $proposal_id)
                       ->update(['university_oob_id' => $oobID]);
                } elseif ($level == 'BOR') {
                    BoardMeetingAgenda::where('board_proposal_id', $proposal_id)
                    ->update(['board_oob_id' => $oobID]);
                }
            }

            $orderOfBusiness->update([
                'status' => 1,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Meeting disseminated successfully!',
                'title' => "Success!"
            ]);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
        }
    }





    /**
     * Get the correct foreign key column based on the level.
     */
    private function getMeetingColumn(string $level): string
    {
        return match ($level) {
            'Local' => 'local_council_meeting_id',
            'University' => 'university_council_meeting_id',
            'BOR' => 'bor_meeting_id',
            default => 'meeting_id',
        };
    }
}
