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
use App\Models\GroupProposal;
use Barryvdh\DomPDF\Facade\Pdf;
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
                    $proposal->files = ProposalFile::where('proposal_id', $proposal->proposal->id)->orderBy('order_no', 'asc')->get();
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

    // GENERATE OOB WITH ORDER NO
    // public function generateOOB(Request $request, String $level, String $meeting_id)
    // {
    //     try {
    //         $request->validate([
    //             'preliminaries' => 'required|string',
    //         ]);
    
    //         $meetingID = decrypt($meeting_id);
    
    //         // Determine the correct models dynamically
    //         $oobModel = match ($level) {
    //             'Local' => LocalOob::class,
    //             'University' => UniversityOob::class,
    //             'BOR' => BoardOob::class,
    //             default => null
    //         };
    
    //         $agendaModel = match ($level) {
    //             'Local' => LocalMeetingAgenda::class,
    //             'University' => UniversityMeetingAgenda::class,
    //             'BOR' => BoardMeetingAgenda::class,
    //             default => null
    //         };
    
    //         if (!$oobModel || !$agendaModel) {
    //             return response()->json([
    //                 'type' => 'danger',
    //                 'message' => 'Invalid meeting level!',
    //                 'title' => "Error!"
    //             ]);
    //         }
    
    //         // Check if an Order of Business already exists
    //         if ($oobModel::where($this->getMeetingColumn($level), $meetingID)->exists()) {
    //             return response()->json([
    //                 'type' => 'info',
    //                 'message' => 'This meeting already has an Order of Business!',
    //                 'title' => "Duplicate Entry"
    //             ]);
    //         }
    
    //         // Create the Order of Business (OOB)
    //         $oob = $oobModel::create([
    //             $this->getMeetingColumn($level) => $meetingID,
    //             'preliminaries' => $request->input('preliminaries'),
    //             'status' => 0,
    //         ]);
    
    //         // Retrieve proposals with status = 1
    //         $proposals = $agendaModel::where($this->getMeetingColumn($level), $meetingID)
    //             ->where('status', 1)
    //             ->get();
    
    //         // Assign order_no sequentially
    //         foreach ($proposals as $index => $proposal) {
    //             $proposal->update([
    //                 // 'local_oob_id' => $oob->id,
    //                 'order_no' => $index + 1,
    //             ]);
    //         }
    
    //         return response()->json([
    //             'type' => 'success',
    //             'message' => 'Order of Business generated successfully with proposals!',
    //             'title' => "Success!"
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'type' => 'danger',
    //             'message' => $th->getMessage(),
    //             'title' => "Something went wrong!"
    //         ]);
    //     }
    // }

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

            $meetingTypes = [
                'Local' => ['model' => LocalMeetingAgenda::class, 'meeting_key' => 'local_council_meeting_id', 'oob_key' => 'local_oob_id'],
                'University' => ['model' => UniversityMeetingAgenda::class, 'meeting_key' => 'university_meeting_id', 'oob_key' => 'university_oob_id'],
                'BOR' => ['model' => BoardMeetingAgenda::class, 'meeting_key' => 'bor_meeting_id', 'oob_key' => 'board_oob_id'],
            ];
            
            if (isset($meetingTypes[$level])) {
                $model = $meetingTypes[$level]['model'];
                $meetingKey = $meetingTypes[$level]['meeting_key'];
                $oobKey = $meetingTypes[$level]['oob_key'];
            
                $query = $model::where($meetingKey, $meeting->id)->with('proposal')
                ->orderBy('order_no', 'asc');
            
                if ($orderOfBusiness->status == 1) {
                    $query->where($oobKey, $orderOfBusiness->id);
                } else {
                    $query->where('status', 1);
                }
            
                $proposals = $query->get();
            }            

            // Get meeting type
            $councilType = $meeting->council_type ?? null;
            $councilTypesConfig = config('proposals.council_types');

            // Initialize categorized proposals
            $categorizedProposals = [];

            // Categorize proposals by type
            foreach ($matters as $type => $title) {
                $categorizedProposals[$type] = collect();
            }

            // Group proposals by type and then by group_proposal_id
            foreach ($proposals as $proposal) {
                $type = $proposal->proposal->type;
                
                if (!isset($categorizedProposals[$type])) {
                    $categorizedProposals[$type] = collect();
                }

                $groupId = $proposal->proposal->group_proposal_id ?? null;

                if ($groupId) {
                    // If grouped, store under the corresponding group
                    if (!isset($categorizedProposals[$type][$groupId])) {
                        $categorizedProposals[$type][$groupId] = collect();
                    }
                    $categorizedProposals[$type][$groupId]->push($proposal);
                } else {
                    // If no group_proposal_id, store as an individual proposal
                    $categorizedProposals[$type][] = $proposal;
                }
            }

            // Attach additional data (proponents and files)
            foreach ($categorizedProposals as &$proposalsGroup) {
                foreach ($proposalsGroup as &$group) {
                    if ($group instanceof \Illuminate\Support\Collection) {
                        foreach ($group as $proposal) {
                            if (isset($proposal->proposal)) { // Ensure proposal exists
                                $proponentIds = explode(',', $proposal->proposal->employee_id ?? '');
                                $proposal->proponentsList = !empty($proponentIds) 
                                    ? User::whereIn('employee_id', $proponentIds)->get() 
                                    : collect();

                                $proposal->files = ProposalFile::where('proposal_id', $proposal->proposal->id)->orderBy('order_no', 'asc')->get() ?? collect();
                            }
                        }
                    } elseif ($group instanceof stdClass || is_object($group)) {
                        // Handle if $group is a direct proposal object (not in a collection)
                        if (isset($group->proposal)) {
                            $proponentIds = explode(',', $group->proposal->employee_id ?? '');
                            $group->proponentsList = !empty($proponentIds) 
                                ? User::whereIn('employee_id', $proponentIds)->get() 
                                : collect();

                            $group->files = ProposalFile::where('proposal_id', $group->proposal->id)->orderBy('order_no', 'asc')->get() ?? collect();
                        }
                    }
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
                'postedToAgendaProposalIDS' => 'required|array|min:1'
            ]);

            $postedToAgendaProposalIDS = $request->input('postedToAgendaProposalIDS');

            foreach ($postedToAgendaProposalIDS as $proposal_id) {
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


    public function exportOOB_PDF(Request $request, String $level, String $oob_id)
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

            $meetingTypes = [
                'Local' => ['model' => LocalMeetingAgenda::class, 'meeting_key' => 'local_council_meeting_id', 'oob_key' => 'local_oob_id'],
                'University' => ['model' => UniversityMeetingAgenda::class, 'meeting_key' => 'university_meeting_id', 'oob_key' => 'university_oob_id'],
                'BOR' => ['model' => BoardMeetingAgenda::class, 'meeting_key' => 'bor_meeting_id', 'oob_key' => 'board_oob_id'],
            ];
            
            if (isset($meetingTypes[$level])) {
                $model = $meetingTypes[$level]['model'];
                $meetingKey = $meetingTypes[$level]['meeting_key'];
                $oobKey = $meetingTypes[$level]['oob_key'];
            
                $query = $model::where($meetingKey, $meeting->id)->with('proposal')
                ->where($oobKey, $orderOfBusiness->id)
                ->orderBy('created_at', 'desc');
            
                $proposals = $query->get();
            }            

            // Get meeting type
            $councilType = $meeting->council_type ?? null;
            $councilTypesConfig = config('proposals.council_types');

            // Initialize categorized proposals
            $categorizedProposals = [];

            // Categorize proposals by type
            foreach ($matters as $type => $title) {
                $categorizedProposals[$type] = collect();
            }

            // Group proposals by type and then by group_proposal_id
            foreach ($proposals as $proposal) {
                $type = $proposal->proposal->type;
                
                if (!isset($categorizedProposals[$type])) {
                    $categorizedProposals[$type] = collect();
                }

                $groupId = $proposal->proposal->group_proposal_id ?? null;

                if ($groupId) {
                    // If grouped, store under the corresponding group
                    if (!isset($categorizedProposals[$type][$groupId])) {
                        $categorizedProposals[$type][$groupId] = collect();
                    }
                    $categorizedProposals[$type][$groupId]->push($proposal);
                } else {
                    // If no group_proposal_id, store as an individual proposal
                    $categorizedProposals[$type][] = $proposal;
                }
            }

            // Attach additional data (proponents and files)
            foreach ($categorizedProposals as &$proposalsGroup) {
                foreach ($proposalsGroup as &$group) {
                    if ($group instanceof \Illuminate\Support\Collection) {
                        foreach ($group as $proposal) {
                            if (isset($proposal->proposal)) { // Ensure proposal exists
                                $proponentIds = explode(',', $proposal->proposal->employee_id ?? '');
                                $proposal->proponentsList = !empty($proponentIds) 
                                    ? User::whereIn('employee_id', $proponentIds)->get() 
                                    : collect();

                                $proposal->files = ProposalFile::where('proposal_id', $proposal->proposal->id)->orderBy('order_no', 'asc')->get() ?? collect();
                            }
                        }
                    } elseif ($group instanceof stdClass || is_object($group)) {
                        // Handle if $group is a direct proposal object (not in a collection)
                        if (isset($group->proposal)) {
                            $proponentIds = explode(',', $group->proposal->employee_id ?? '');
                            $group->proponentsList = !empty($proponentIds) 
                                ? User::whereIn('employee_id', $proponentIds)->get() 
                                : collect();

                            $group->files = ProposalFile::where('proposal_id', $group->proposal->id)->orderBy('order_no', 'asc')->get() ?? collect();
                        }
                    }
                }
            }
            // dd($categorizedProposals);
            
            $pdf = Pdf::loadView('pdf.export_oob_pdf', compact('orderOfBusiness', 'categorizedProposals', 'meeting', 'matters'))
            ->setPaper('A4', 'portrait');
            
            $oob_filename = "";
            if($meeting->getMeetingCouncilType() == 0){
                $oob_filename = config('meetings.quaterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.local_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
            } else  if($meeting->getMeetingCouncilType() == 1){
                $oob_filename = config('meetings.quaterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.university_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
            }
            else  if($meeting->getMeetingCouncilType() == 2){
                $oob_filename = config('meetings.quaterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.board_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
            }
        
            return $pdf->stream($oob_filename);
        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }

    // UPDATE PROPOSAL ORDER NUMBER
    public function updateProposalOrder(Request $request, String $level)
    {
        try {
            $orderData = $request->input('orderData');

            // Determine the correct Model class based on the level
            $agendaModel = match ($level) {
                'Local' => LocalMeetingAgenda::class,
                'University' => UniversityMeetingAgenda::class,
                'BOR' => BoardMeetingAgenda::class,
                default => null
            };

            // Ensure the model is valid
            if (!$agendaModel) {
                return response()->json(['error' => 'Invalid level provided.'], 400);
            }

            foreach ($orderData as $item) {
                $agendaModel::where($this->getProposalAgendaColumn($level), $item['id'])
                    ->update(['order_no' => $item['position']]);
            }

            return response()->json(['message' => 'Order updated successfully!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    // SAVE PROPOSAL GROUP
    public function saveProposalGroup(Request $request, String $level)
    {
        try {
            // Validate the request
            $data = $request->validate([
                'group_title' => 'required|string',
                'order_no' => 'required',
                'proposals' => 'required|array',
                'proposals.*' => 'integer|exists:proposals,id',
            ]);
    
            // Determine the correct agenda model
            $agendaModel = match ($level) {
                'Local' => LocalMeetingAgenda::class,
                'University' => UniversityMeetingAgenda::class,
                'BOR' => BoardMeetingAgenda::class,
                default => null
            };
    
            if (!$agendaModel) {
                return response()->json(['error' => 'Invalid meeting level'], 400);
            }
    
            // Create new group
            $group = GroupProposal::create([
                'group_title' => $data['group_title'],
                'order_no' => $data['order_no']
            ]);
    
            // Assign group and update order
            foreach ($data['proposals'] as $index => $proposalId) {
                $agendaModel::where($this->getProposalAgendaColumn($level), $proposalId)
                    ->update([
                        'group_proposal_id' => $group->id,
                        'order_no' => $index + 1 
                    ]);
            }
    
            return response()->json(['type'=> 'success','message' => 'Group created and order updated successfully!', 'title' => 'Success']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    // UNGROUP PROPOSAL
    public function ungroupProposal(Request $request, String $level)
    {
        try {
            $groupId = $request->input('group_id');

            // Determine the correct agenda model
            $agendaModel = match ($level) {
                'Local' => LocalMeetingAgenda::class,
                'University' => UniversityMeetingAgenda::class,
                'BOR' => BoardMeetingAgenda::class,
                default => null
            };

            if (!$agendaModel) {
                return response()->json(['error' => 'Invalid meeting level'], 400);
            }

            // Remove group_proposal_id from proposals in the agenda
            $agendaModel::where('group_proposal_id', $groupId)
                ->update(['group_proposal_id' => null]);

            // Soft delete the group proposal
            GroupProposal::where('id', $groupId)->delete();

            return response()->json(['type' => 'success', 'message' => 'Proposals ungrouped successfully!', 'title' => 'Success']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function updateProposalGroup(Request $request, String $level)
    {
        try {
            // Validate the request
            $data = $request->validate([
                'group_id' => 'required|integer|exists:group_proposals,id',
                'group_title' => 'required|string',
                'order_no' => 'required',
            ]);

            // Update the group
            $group = GroupProposal::findOrFail($data['group_id']);
            $group->update([
                'group_title' => $data['group_title'],
                'order_no' => $data['order_no']
            ]);

            return response()->json(['type' => 'success', 'message' => 'Group updated successfully!', 'title' => 'Success']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    // FILTER MEETINGS
    public function filterOOB(Request $request){
        $role = session('user_role');
        $campus_id = session('campus_id');

        $request->validate([
            'level' => 'required|integer',
        ]);

        $oobLevel = match ($request->input('level')) {
            0 => 'Local',
            1 => 'University',
            2 => 'BOR',
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
            if((session('user_role') == 3 || session('isProponent')) && $oobLevel == 0){
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

        return response()->json([
            'type' => 'success',
            'html' => view('content.orderOfBusiness.partials.oob_table', compact('orderOfBusiness'))->render() 
        ]);
    }



    /**
     * Get the correct Meeting foreign key column based on the level.
     */
    private function getMeetingColumn(string $level): string
    {
        return match ($level) {
            'Local' => 'local_council_meeting_id',
            'University' => 'university_council_meeting_id',
            'BOR' => 'bor_meeting_id',
            default => '',
        };
    }

        /**
     * Get the correct Proposal foreign key column based on the level.
     */
    private function getProposalAgendaColumn(string $level): string
    {
        return match ($level) {
            'Local' => 'local_proposal_id',
            'University' => 'university_proposal_id',
            'BOR' => 'board_proposal_id',
            default => '',
        };
    }
}
