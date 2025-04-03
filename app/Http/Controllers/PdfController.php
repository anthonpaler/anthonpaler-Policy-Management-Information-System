<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Models\LocalMeetingAgenda;
use App\Models\UniversityMeetingAgenda;
use App\Models\BoardMeetingAgenda;
use App\Models\LocalOob;
use App\Models\UniversityOob;
use App\Models\BoardOob;
use App\Models\OtherMatter;


class PdfController extends Controller
{
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

            $oob_filename = "";
            if($meeting->getMeetingCouncilType() == 0){
                $oob_filename = config('meetings.quaterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.local_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
            } else  if($meeting->getMeetingCouncilType() == 1){
                $oob_filename = config('meetings.quaterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.university_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
            }
            else  if($meeting->getMeetingCouncilType() == 2){
                $oob_filename = config('meetings.quaterly_meetings.'.$meeting->quarter)." ".config('meetings.council_types.board_level.'.$meeting->council_type)." ".$meeting->year.'.pdf';
            }

            $this->oob_layout_PDF( $categorizedProposals, $oob_filename, $meeting, $matters, $orderOfBusiness,  $otherMattersProposals, $otherMattersTitle);

        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'danger',
                'message' => $th->getMessage(),
                'title' => "Something went wrong!"
            ]);
        }
    }

    public function oob_layout_PDF($categorizedProposals, $oob_filename, $meeting,  $matters, $orderOfBusiness, $otherMattersProposals, $otherMattersTitle)
    {
        $pdf = new CustomHeaderFooterTCPDF();

        // Document information
        $pdf->SetTitle($oob_filename);
        $pdf->SetAuthor('Policy Management Information System (PolMIS)');

        // Set margins (left, top, right)
        $pdf->SetMargins(25.4, 37, 25.4);

        $pdf->SetAutoPageBreak(true, 37);

        $pdf->AddPage();

        // $pesoSign = html_entity_decode('&#8369;', ENT_QUOTES, 'UTF-8');
        $pesoSign = 'P';


        // Font
        $cambriaPath = base_path('vendor/elibyy/tcpdf-laravel/src/fonts/Cambria.ttf');
        $cambriaBoldPath = base_path('vendor/elibyy/tcpdf-laravel/src/fonts/CAMBRIAB.TTF');
        $cambria = \TCPDF_FONTS::addTTFfont($cambriaPath, 'TrueTypeUnicode', '', 32);
        $cambriaBold = \TCPDF_FONTS::addTTFfont($cambriaBoldPath, 'TrueTypeUnicode', '', 32);

        $pdf->SetFont($cambria, 'I', 10);
        $pdf->SetTextColor(0, 0, 0);

        // **Header Section**
        $pdf->Cell(0, 5,   mb_strtoupper(config('meetings.quaterly_meetings.'.$meeting->quarter) . ' '. $meeting->year, 'UTF-8'), 0, 1, 'C');
        $pdf->SetFont($cambriaBold, 'B', 10);

        $title = "";
        if ($meeting->getMeetingCouncilType() == 0){
            $title =config('meetings.council_types.local_level.'.$meeting->council_type);
        } else if($meeting->getMeetingCouncilType() == 1){
            $title = config('meetings.council_types.university_level.'.$meeting->council_type) ;
        } else if($meeting->getMeetingCouncilType() == 2){
            $title = config('meetings.council_types.board_level.'.$meeting->council_type) ;
        }

        $pdf->Cell(0, 5,  mb_strtoupper($title, 'UTF-8'), 0, 1, 'C');
        $pdf->SetFont($cambria, '', 9);

        $meetingModality = '';
        if ($meeting->modality == 1 || $meeting->modality == 3) {
            $meetingModality .= ' | Venue at ' . $meeting->venue->name;
        }
        if ($meeting->modality == 2 || $meeting->modality == 3) {
            $meetingModality .= ' | Via ' . config('meetings.mode_if_online_types.' . $meeting->mode_if_online) . ' - Online';
        }
        if (empty($meetingModality)) {
            $meetingModality = ' | Venue or platform not yet set';
        }
        $meetingDateTime  = \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, l, h:i A');
        $pdf->Cell(0, 5, $meetingDateTime.' '.$meetingModality , 0, 1, 'C');

        $pdf->Ln(5);

        $pdf->SetFont($cambriaBold, 'B', 11);
        $pdf->Cell(0, 6, 'ORDER OF BUSINESS', 0, 1, 'C');
        $pdf->Ln(3);

        // **Preliminaries Section**
        $pdf->SetFont($cambriaBold, 'B', 9);
        $pdf->Cell(0, 6, '1. Preliminary', 0, 1, 'L');
        $pdf->SetFont($cambria, '', 8);
        $pdf->SetLeftMargin(35);
        $htmlPrelim = '<p>' . nl2br(e($orderOfBusiness->preliminaries)) . '</p>';
        $pdf->writeHTML($htmlPrelim, true, false, true, false, '');
        $pdf->SetLeftMargin(25.4);
        $pdf->Ln(2);

        // **New Business Section**
        $pdf->SetFont($cambriaBold, 'B', 9);
        $pdf->Cell(0, 6, '2. New Business', 0, 1, 'L');
        $pdf->Ln(3);
        $pdf->SetFont($cambria, '', 8);

        $counter = 1;
        $groupCounter = 1;
        $noProposals = collect($categorizedProposals)->flatten()->isEmpty();
        $allProposalIds = collect($categorizedProposals)->flatten()->pluck('id');

        $style = '<style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        font-size: 8px;
                    }
                    th {
                        background-color: #078EED;
                        color: #FFFFFF;
                        text-align: center;
                        padding: 10px;
                        border: .5px solid #AACFEA;
                        font-weight: bold;
                    }
                    .table-header{
                        background-color: #0071C1;
                        font-size: 10px;
                        text-transform: uppercase;
                    }
                    .tr-group{
                        background-color:rgb(191, 218, 248);
                    }
                    td {
                        text-align: left;
                        border: .5px solid #AACFEA;
                        padding: 10px;
                    }
                    .group-item{
                        padding-left: 30px;
                    }
                </style>';
        $html = $style;
        if ($noProposals) {
            $html .= '<p style="color: red; text-align: center;">No new order of business available at the moment.</p>';
        } else {
            foreach ($matters as $type => $mattertTitle) {
                $allProposals = collect();


                // Add standalone proposals
                foreach ($categorizedProposals[$type]->whereNull('group_proposal_id') as $proposal) {
                    $allProposals->push([
                        'type' => 'individual',
                        'order_no' => $proposal->order_no,
                        'data' => $proposal
                    ]);
                }

                // Add grouped proposals
                foreach ($categorizedProposals[$type]->whereNotNull('group_proposal_id')->groupBy('group_proposal_id') as $groupID => $proposals) {
                    $groupOrderNo = $proposals->first()->proposal_group->order_no ?? 9999;
                    $allProposals->push([
                        'type' => 'group',
                        'order_no' => $groupOrderNo,
                        'group_id' => $groupID,
                        'data' => $proposals
                    ]);
                }

                // Sort by order_no
                $allProposals = $allProposals->sortBy('order_no');

                if ($categorizedProposals[$type]->count() > 0) {
                    $html .= '<table cellpadding="5">
                            <thead>
                                <tr>
                                    <th colspan="4" class="table-header">' .  mb_strtoupper($mattertTitle, 'UTF-8') . '</th>
                                </tr>
                                <tr>
                                    <th width="10%">No.</th>
                                    <th width="45%">Title of the Proposal</th>
                                    <th width="25%">Presenters</th>
                                    <th width="20%">Requested Action</th>
                                </tr>
                            </thead>
                            <tbody>';

                    foreach ($allProposals as $proposal) {
                        if ($proposal['type'] === 'individual') {

                            $presenters = isset($proposal['data']->proposal->proponents) && $proposal['data']->proposal->proponents->isNotEmpty()
                                ? implode(', ', $proposal['data']->proposal->proponents->pluck('name')->toArray())
                                : '<span>No presenters</span>';

                            $requestedAction = config('proposals.requested_action.' . $proposal['data']->proposal->action) ?? 'N/A';

                            $html .= '
                                <tr>
                                    <td width="10%">2.' . $counter . '</td>
                                    <td width="45%">' . str_replace('₱', '<span style="font-family: dejavusans;">₱</span>', $proposal['data']->proposal->title) . '</td>
                                    <td width="25%">' . $presenters . '</td>
                                    <td width="20%">' . htmlspecialchars($requestedAction) . '</td>
                                </tr>';
                            $counter++;
                        } else {
                            $groupTitle = $proposal['data']->first()->proposal_group->group_title ?? 'Group Proposal';
                            $html .= '
                                <tr class="tr-group">
                                    <td width="10%">2.' . $counter . '</td>
                                    <td colspan="3" width="90%">' . htmlspecialchars($groupTitle) . '</td>
                                </tr>';

                            foreach ($proposal['data'] as $groupedProposal) {
                                $presenters = $groupedProposal->proposal->proponents->isNotEmpty()
                                    ? implode(', ', $groupedProposal->proposal->proponents->pluck('name')->toArray())
                                    : '<span>No presenters</span>';

                                $requestedAction = config('proposals.requested_action.' . $groupedProposal->proposal->action) ?? 'N/A';

                                $html .= '
                                    <tr>
                                        <td width="10%" class="group-item"><span>     </span> 2.' . $counter . '.' . $groupCounter . '</td>
                                        <td width="45%">' .  str_replace('₱', '<span style="font-family: dejavusans;">₱</span>', $groupedProposal->proposal->title) . '</td>
                                        <td width="25%">' . $presenters . '</td>
                                        <td width="20%">' . htmlspecialchars($requestedAction) . '</td>
                                    </tr>';
                                $groupCounter++;
                            }
                            $counter++;
                            $groupCounter = 1;
                        }
                    }
                    $html .= '</tbody>
                        </table>';

                    $html .= '<div> </div>';
                }
            }

        }

        $pdf->writeHTML($html, true, false, true, false, '');

        $html2 = $style;

         // Other Matters Section
        if ($otherMattersProposals->isNotEmpty()) {
            $pdf->SetFont($cambria, 'B', 9);
            $pdf->Cell(0, 6, '3. Other Matters', 0, 1, 'L');
            $pdf->Ln(3);
            $pdf->SetFont($cambria, '', 8);

            $html2 .= '<table cellpadding="5">
                    <thead>
                        <tr>
                            <th colspan="4" class="table-header">' .  mb_strtoupper($otherMattersTitle, 'UTF-8') . '</th>
                        </tr>
                        <tr>
                            <th width="10%">No.</th>
                            <th width="45%">Title of the Proposal</th>
                            <th width="25%">Presenters</th>
                            <th width="20%">Requested Action</th>
                        </tr>
                    </thead>
                    <tbody>';

            $counter = 1;
            foreach ($otherMattersProposals as $otherMatter){

                $presenters = isset($otherMatter->proposal->proponents) && $otherMatter->proposal->proponents->isNotEmpty()
                ? implode(', ', $otherMatter->proposal->proponents->pluck('name')->toArray())
                : '<span>No presenters</span>';

                $html2 .= '
                    <tr>
                        <td width="10%">3.' . $counter . '</td>
                        <td width="45%">' .  str_replace('₱', '<span style="font-family: dejavusans;">₱</span>', $otherMatter->proposal->title) . '</td>
                        <td width="25%">' . $presenters . '</td>
                    <td width="20%">'.config('proposals.requested_action.' . $otherMatter->proposal->action).'</td>



                    </tr>';


                    $counter++;
            }
            $html2 .= '</tbody></table>'; // Close the table properly
        } else {
            // Show message if no proposals are available
            $html2 .= '<p style="color: red; text-align: center;">No other matters available at the moment.</p>';
        }
        $pdf->writeHTML($html2, true, false, true, false, '');

        ob_end_clean();

        // Output the PDF
        return response()->make($pdf->Output($oob_filename, 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$oob_filename.'"'
        ]);

    }
}

// Custom TCPDF Class with Header and Footer
class CustomHeaderFooterTCPDF extends \TCPDF
{
    public function Header()
    {
        $cambriaPath = base_path('vendor/elibyy/tcpdf-laravel/src/fonts/Cambria.ttf');
        $cambria = \TCPDF_FONTS::addTTFfont($cambriaPath, 'TrueTypeUnicode', '', 32);

        $bagong_pilipinas_img= public_path('assets/img/pdf_images/bagong_pilipinas.png');
        $slsu_logo_img = public_path('assets/img/pdf_images/slsu_logo.png');

        if (file_exists($bagong_pilipinas_img)) {
            $this->Image($slsu_logo_img, 50, 3, 75, 0, 'PNG');
            $this->Image($bagong_pilipinas_img, 135, 3, 20, 0, 'PNG');
        }


        $this->SetY(25);
        $this->SetFont($cambria, '', 6.5);
        $this->Cell(0, 6, 'Excellence | Service | Leadership and Good Governance | Innovation | Social Responsibility | Integrity | Professionalism | Spirituality', 0, 1, 'C');

        $this->SetY(-15);

        // Draw a line on top of the footer
        $this->SetDrawColor(0, 0, 0); // Black color
        $this->SetLineWidth(0.1); // Line thickness
        $this->Line(25.4, $this->GetY() - 251, 185, $this->GetY() - 251); // (x1, y1, x2, y2)
    }

    public function Footer()
    {
        $cambriaPath = base_path('vendor/elibyy/tcpdf-laravel/src/fonts/Cambria.ttf');
        $cambria = \TCPDF_FONTS::addTTFfont($cambriaPath, 'TrueTypeUnicode', '', 32);

        $stars_rating_systemIMG = public_path('assets/img/pdf_images/stars_rating_system.png');
        $socotecIMG = public_path('assets/img/pdf_images/socotec.png');


        $this->SetY(-17);

        // Draw a line on top of the footer
        $this->SetDrawColor(0, 0, 0); // Black color
        $this->SetLineWidth(0.1); // Line thickness
        $this->Line(25.4, $this->GetY() - 12, 185, $this->GetY() - 12); // (x1, y1, x2, y2)

        if (file_exists($stars_rating_systemIMG)) {
            $this->Image($stars_rating_systemIMG, 73, 272, 45, 0, 'PNG');
            $this->Image($socotecIMG, 151, 272, 34, 0, 'PNG');
        }

        $this->SetFont($cambria, 'I', 10);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

        if ($this->page == $this->getNumPages()) {
            $this->SetY(-34);
            $this->SetFont($cambria, '', 7);

            $this->Cell(50, 5, 'Generated on: ' . date('F d, Y h:i A'), 0, 0, 'L');
            $this->Cell(79, 5, '', 0, 0, 'L');
            $this->Cell(30, 5, 'Generated through Policy Management Information System', 0, 0, 'R');
        }

    }
}
