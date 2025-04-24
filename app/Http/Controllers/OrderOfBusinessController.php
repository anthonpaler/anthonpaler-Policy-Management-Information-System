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
use App\Models\GroupProposalFiles;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Venues;
use Illuminate\Support\Facades\DB;
use App\Mail\OOBNotification;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\SMSController;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\HrmisEmployee;
use App\Models\OtherMatter;



class OrderOfBusinessController extends Controller
{
    // VIEW GENERATE OOB
    public function viewGenerateOOB(Request $request, String $level, String $meeting_id)
    {
        try {
            $meetingID = decrypt($meeting_id);
            $proposals = collect();
            $matters = [0 => 'Financial Matters'] + config('proposals.matters');
            $meeting = null;

            $meetingTypes = [
                'Local' => ['agendaModel' => LocalMeetingAgenda::class,
                            'meeting_key' => 'local_council_meeting_id',
                            'meetingModel' => LocalCouncilMeeting::class],
                'University' => ['agendaModel' => UniversityMeetingAgenda::class,
                            'meeting_key' => 'university_meeting_id',
                            'meetingModel' => UniversityCouncilMeeting::class],
                'BOR' => ['agendaModel' => BoardMeetingAgenda::class,
                            'meeting_key' => 'bor_meeting_id',
                            'meetingModel' => BorMeeting::class],
            ];


            if (isset($meetingTypes[$level])) {
                $agendaModel = $meetingTypes[$level]['agendaModel'];
                $meetingKey = $meetingTypes[$level]['meeting_key'];
                $meetingModel = $meetingTypes[$level]['meetingModel'];

                $meeting = $meetingModel::find($meetingID);

                $proposals = $agendaModel::where($meetingKey, $meeting->id)
                    ->with('proposal')
                    ->orderBy('order_no', 'asc')
                    ->where('status', 1)
                    ->get();
            }

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
        DB::beginTransaction();

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

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Order of Business generated successfully!',
                'title' => "Success!"
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


    public function uploadPreviousMinutes(Request $request)
    {
        $request->validate([
            'previous_minutes' => 'required|mimes:pdf,doc,docx|max:10240',
            'meeting_id' => 'required|integer',
        ]);

        $file = $request->file('previous_minutes');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/previous_minutes', $fileName);

        $meetingId = $request->meeting_id;

        // Map meeting types to their corresponding Oob models
        $meetingMapping = [
            'local' => [LocalCouncilMeeting::class, LocalOob::class, 'local_council_meeting_id'],
            'university' => [UniversityCouncilMeeting::class, UniversityOob::class, 'university_council_meeting_id'],
            'board' => [BorMeeting::class, BoardOob::class, 'bor_meeting_id']
        ];

        $oob = null;

        foreach ($meetingMapping as $type => [$meetingModel, $oobModel, $foreignKey]) {
            if ($meetingModel::where('id', $meetingId)->exists()) {
                $oob = $oobModel::where($foreignKey, $meetingId)->first();

                // If OOB doesn't exist, create it
                if (!$oob) {
                    $oob = $oobModel::create([$foreignKey => $meetingId]);
                }

                break;
            }
        }



        if (!$oob) {
            return response()->json(['success' => false, 'message' => 'Meeting not found'], 404);
        }


        if (!empty($oob->previous_minutes)) {
            Storage::delete("public/previous_minutes/{$oob->previous_minutes}");
        }

        // Update the OOB record with the uploaded file
        $oob->update(['previous_minutes' => $fileName]);

        return response()->json(['success' => true, 'message' => 'Previous minutes uploaded successfully']);
    }


    public function getPreviousMinutes($meetingId)
    {
      // Map meeting types to their corresponding Oob models
      $meetingMapping = [
          'local' => [LocalOob::class, 'local_council_meeting_id'],
          'university' => [UniversityOob::class, 'university_council_meeting_id'],
          'board' => [BoardOob::class, 'bor_meeting_id']
      ];

      $orderOfBusiness = null;

      foreach ($meetingMapping as [$oobModel, $foreignKey]) {
          $orderOfBusiness = $oobModel::where($foreignKey, $meetingId)->first();
          if ($orderOfBusiness) {
              break;
          }
      }

      if ($orderOfBusiness && !empty($orderOfBusiness->previous_minutes)) {
          return response()->json([
              'success' => true,
              'previous_minutes' => $orderOfBusiness->previous_minutes
          ]);
      }

      return response()->json([
          'success' => false,
          'previous_minutes' => null
      ]);
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
            }elseif(session('user_role') == 8){
                $orderOfBusiness = BoardOob::with('meeting' )
                ->orderBy('created_at', 'desc')
                ->get();
            }
            else{
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
            $otherMattersProposals = collect();
            $matters = [0 => 'Financial Matters'] + config('proposals.matters');
            $otherMattersTitle = 'Other Matters';
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

            // Retrieve previous minutes if available
            $previousMinutes = $orderOfBusiness->previous_minutes
            ? asset("storage/previous_minutes/{$orderOfBusiness->previous_minutes}")
            : null;

            $meetingTypes = [
                'Local' => ['model' => LocalMeetingAgenda::class, 'meeting_key' => 'local_council_meeting_id', 'oob_key' => 'local_oob_id'],
                'University' => ['model' => UniversityMeetingAgenda::class, 'meeting_key' => 'university_meeting_id', 'oob_key' => 'university_oob_id'],
                'BOR' => ['model' => BoardMeetingAgenda::class, 'meeting_key' => 'bor_meeting_id', 'oob_key' => 'board_oob_id'],
            ];
            if (isset($meetingTypes[$level])) {
              $model = $meetingTypes[$level]['model'];
              $meetingKey = $meetingTypes[$level]['meeting_key'];
              $oobKey = $meetingTypes[$level]['oob_key'];

              $query = $model::where($meetingKey, $meeting->id)
                  ->with('proposal')
                  ->orderBy('order_no', 'asc')
                  ->where(function ($q) use ($oobKey, $orderOfBusiness) {
                      $q->where($oobKey, $orderOfBusiness->id)
                          ->orWhere('status', 1);
                  });

              // Fetch all proposals
              $allProposals = $query->get();

              // Separate proposals into regular ones and other matters
              foreach ($allProposals as $proposal) {
                  if ($proposal->proposal->isOtherMatter()) {
                      $otherMattersProposals->push($proposal); // for other matter proposals
                  } else {
                      $proposals->push($proposal); // for proposals under new business
                  }
              }
            }
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

                $groupId = $proposal->proposal->group_proposal_id ?? null;
                if ($groupId) {
                    if (!isset($categorizedProposals[$type][$groupId])) {
                        $categorizedProposals[$type][$groupId] = collect();
                    }
                    $categorizedProposals[$type][$groupId]->push($proposal);
                } else {
                    $categorizedProposals[$type][] = $proposal;
                }
            }

            return view('content.orderOfBusiness.viewOOB', compact(
                'orderOfBusiness',
                'meeting',
                'previousMinutes',
                'categorizedProposals',
                'matters',
                'otherMattersProposals',
                'otherMattersTitle'
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
        set_time_limit(300);// Increase execution time to 5 minutes for this request

        DB::beginTransaction();
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

            // Fetch Employees based on Council Type
            $employeeQuery = Employee::query();

            if ($level == 'BOR') {
                // Only include BOR members for BOR level
                $borMemberIds = DB::table('bor_member')->pluck('employee_id')->toArray();
                $employeeQuery->whereIn('id', $borMemberIds);
            } else {
                // Otherwise, filter based on council type
                if ($orderOfBusiness->meeting->council_type == 2) { // Academic Council
                    $employeeQuery->whereIn('id', function ($query) {
                        $query->select('employee_id')->from('academic_council_membership');
                    });
                } elseif ($orderOfBusiness->meeting->council_type == 3) { // Administrative Council
                    $employeeQuery->whereIn('id', function ($query) {
                        $query->select('employee_id')->from('administrative_council_membership');
                    });
                } else { // Joint Council (Both Academic and Administrative)
                    $employeeQuery->whereIn('id', function ($query) {
                        $query->select('employee_id')->from('academic_council_membership')
                            ->union(
                                DB::table('administrative_council_membership')->select('employee_id')
                            );
                    });
                }
            
                // Filter by campus for Local
                if ($level == 'Local') {
                    $employeeQuery->where('campus', $orderOfBusiness->meeting->campus_id);
                }
            }

            // Get email addresses and cellphone numbers
            $emails = $employeeQuery->pluck('EmailAddress')->toArray();
            $cellNumbers = $employeeQuery->pluck('Cellphone')->toArray();

            // Email Notification (in Batches)
            $chunks = array_chunk($emails, 50);
            foreach ($chunks as $batch) {
                Mail::to($batch)->send(new OOBNotification($orderOfBusiness, $orderOfBusiness->meeting));
                sleep(2); // Pause to prevent timeout
            }

            //     // SMS/Text Blast Notification
                $smsController = new SMSController();
                $meeting = $orderOfBusiness->meeting;
                $quarter = config('meetings.quarterly_meetings')[$meeting->quarter] ?? '';
                $council_type = "";
                if ($meeting->getMeetingCouncilType() == 0){
                    $council_type = config('meetings.council_types.local_level.'.$meeting->council_type) ;
                }
                elseif ($meeting->getMeetingCouncilType() == 1){
                    $council_type = config('meetings.council_types.university_level.'.$meeting->council_type) ;
                }
                elseif ($meeting->getMeetingCouncilType() == 2){
                    $council_type = config('meetings.council_types.board_level.'.$meeting->council_type) ;
                }


                $meetingDateTime = date('M j, Y g:i A', strtotime($orderOfBusiness->meeting->meeting_date_time));
                $message = "NOTICE: The provisional agenda (Order of Business) for the $quarter – $council_type meeting this $meetingDateTime is now available.";

                foreach ($emails as $index => $email) {
                    $phone = $cellNumbers[$index] ?? null;

                    if (empty($phone)) {
                        $hrmisEmployee = HrmisEmployee::where('EmailAddress', $email)->first();
                        $phone = $hrmisEmployee?->Cellphone;
                    }

                    if (!empty($phone)) {
                        $smsResponse = $smsController->send($phone, $message);
                        if ($smsResponse['Error'] == 1) {
                            \Log::error("SMS Failed to $phone: " . $smsResponse['Message']);
                        }
                    }
                }


            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' =>'Order of Business disseminated successfully! Notifications sent to Council Members.',
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
          $otherMattersProposals = collect();
          $matters = [0 => 'Financial Matters'] + config('proposals.matters');
          $otherMattersTitle = 'Other Matters';
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


            $query = $model::where($meetingKey, $meeting->id)
                ->with('proposal')
                ->orderBy('order_no', 'asc')
                ->where(function ($q) use ($oobKey, $orderOfBusiness) {
                    $q->where($oobKey, $orderOfBusiness->id);
                });

            // Fetch all proposals
            $allProposals = $query->get();

            // Separate proposals into regular ones and other matters
            foreach ($allProposals as $proposal) {
                if ($proposal->proposal->isOtherMatter()) {
                    $otherMattersProposals->push($proposal); // for other matter proposals
                } else {
                    $proposals->push($proposal); // for proposals under new business
                }
            }
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
              $subType = $proposal->proposal->sub_type;

              if (!isset($categorizedProposals[$type])) {
                  $categorizedProposals[$type] = collect();
              }

              // Separate Financial Matters from Administrative Matters
              if ($type == 2 && $subType == 0) {
                  $type = 0;
              }

              $groupId = $proposal->proposal->group_proposal_id ?? null;

              if ($groupId) {
                  if (!isset($categorizedProposals[$type][$groupId])) {
                      $categorizedProposals[$type][$groupId] = collect();
                  }
                  $categorizedProposals[$type][$groupId]->push($proposal);
              } else {
                  $categorizedProposals[$type][] = $proposal;
              }
          }

            // dd($categorizedProposals);

          $pdf = Pdf::loadView('pdf.export_oob_pdf', compact('orderOfBusiness', 'categorizedProposals', 'meeting', 'matters', 'otherMattersProposals', 'otherMattersTitle'))
            ->setOption([
              'fontDir' => public_path('fonts'), // Set fontDir to the directory containing your fonts
              'fontCache' => public_path('fonts'), // Set fontCache to the same directory or a cache folder
              'defaultFont' => 'Cambria', // Use Cambria as the default font
              "isHtml5ParserEnabled", true,
              "isPhpEnabled", true
          ])
          ->setPaper('A4', 'portrait');


          $oob_filename = "";
          if($meeting->getMeetingCouncilType() == 0){
              $oob_filename = config('meetings.quarterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.local_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
          } else  if($meeting->getMeetingCouncilType() == 1){
              $oob_filename = config('meetings.quarterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.university_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
          }
          else  if($meeting->getMeetingCouncilType() == 2){
              $oob_filename = config('meetings.quarterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.board_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
          }

          // dd($orderOfBusiness, $categorizedProposals, $meeting, $matters, $otherMattersProposals, $otherMattersTitle);
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
      DB::beginTransaction();
      try {
        $orderData = $request->input('orderData');
        // Determine the correct Model class based on the level
        $agendaModel = match ($level) {
            'Local' => LocalMeetingAgenda::class,
            'University' => UniversityMeetingAgenda::class,
            'BOR' => BoardMeetingAgenda::class,
            default => null
        };

        if (!$agendaModel) {
            return response()->json(['error' => 'Invalid level provided.'], 400);
        }
        foreach ($orderData as $item) {
          $isGroup = $item['isGroup'] === 'true';
          $isGroupAttachment = $item['isGroupAttachment'] === 'true';

          if (!$isGroup) {
            if ($isGroupAttachment) {
              GroupProposalFiles::where('id', decrypt($item['id']))
                ->update(['order_no' => $item['order']]);
            } else {
              $agendaModel::where($this->getProposalAgendaColumn($level), decrypt($item['id']))
                ->update(['order_no' => $item['order']]);
            }
          } else {
            GroupProposal::where('id', decrypt($item['id']))
              ->update(['order_no' => $item['order']]);
          }
        }

        DB::commit();
        return response()->json(['message' => 'Order updated successfully!'], 200);
      } catch (\Throwable $th) {
        DB::rollBack();
        return response()->json(['error' => $th->getMessage()], 500);
      }
    }

    // FILTER MEETINGS
    public function filterOOB(Request $request) {
        try {
            $role = session('user_role');
            $campus_id = session('campus_id');

            // Validate level input
            $request->validate([
                'level' => 'required|integer',
            ]);

            $level = (int) $request->input('level');

            // Determine the OOB level
            $oobLevel = match ($level) {
                0 => 'Local',
                1 => 'University',
                2 => 'BOR',
                default => 'Local'
            };

            // Select the appropriate model
            $oobModel = match ($oobLevel) {
                'Local' => LocalOob::class,
                'University' => UniversityOob::class,
                'BOR' => BoardOob::class,
                default => null
            };

            if (!$oobModel) {
                return abort(404, 'Invalid Order of Business Level');
            }

            // Base Query
            $orderOfBusinessQuery = $oobModel::with('meeting');

            // Apply filters based on session role
            if (session('isProponent') && $oobLevel === 'Local') {
                $orderOfBusinessQuery->whereHas('meeting', function ($query) use ($campus_id) {
                    $query->where('campus_id', $campus_id);
                })->where('status', 1);
            } elseif ((session('user_role') == 3 || session('isProponent')) && $oobLevel === 'Local') {
                $orderOfBusinessQuery->whereHas('meeting', function ($query) use ($campus_id) {
                    $query->where('campus_id', $campus_id);
                });
            }

            // Secretary-Level Permissions
            if (session('secretary_level') == $level && !session('isProponent')) {
                $orderOfBusiness = $orderOfBusinessQuery->orderBy('created_at', 'desc')->get();
            } else {
                $orderOfBusiness = $orderOfBusinessQuery->where('status', 1)->orderBy('created_at', 'desc')->get();
            }

            // Return response
            return response()->json([
                'type' => 'success',
                'html' => view('content.orderOfBusiness.partials.oob_table', compact('orderOfBusiness'))->render()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }


    public function saveOOB(Request $request, String $level ,String $oob_id){
        try{
            $oobID = decrypt($oob_id);

            $request->validate([
                'preliminaries' => 'required|string',
            ]);

            $oobModel = match ($level) {
                'Local' => LocalOob::class,
                'University' => UniversityOob::class,
                'BOR' => BoardOob::class,
                default => null
            };

            $orderOfBusiness =  $oobModel::where('id', $oobID)
            ->update( [
                'preliminaries' => $request->input('preliminaries'),
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Order of Business Save successfully!', 'title'=> "Success!"
            ]);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'danger', 'message' => $th->getMessage(), 'title'=> "Something went wrong!"]);
        }
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
   
}
