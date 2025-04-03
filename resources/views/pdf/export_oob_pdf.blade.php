<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order of Business</title>

    <style>
        .page-break {
            page-break-inside: auto;
        }

        p {
            margin: 3px;
            padding-left: 30px;
            font-size: 13px;
        }

        header {
            position: fixed;
            top: -30px;
            left: 47px;
            height: 1.5cm;
        }

        body {
            margin-top: 2.7cm;
            margin-left: 1.2cm;
            margin-right: 1.3cm;
            margin-bottom: 1.3cm; 
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 1cm;
            right: 1cm;
            height: 2cm;
        }
        .th-col{
            height: 30px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            font-size: 12px;
            border: 1px solid rgb(137, 180, 211);
            background-color:rgb(224, 236, 244);
            padding: 5px; /* Reduce padding to remove extra space */
            margin: 0; /* Ensure no extra margin */
        }
        td {
            padding: 5px 10px;
        }
        .table-title {
            padding: 15px !important;
            background-color: #0071C1;
            border: 1px solid rgb(30, 90, 134);
            color: white;
            text-transform: uppercase;
        }
        .tr-group{
            background-color:rgb(175, 207, 228) !important;
        }
        .section-title{
            font-size: 13px;
            font-weight: bold;
            margin: 0;
            margin-top: 20px;
        }
        .heading{
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
        }
        .heading-1, .heading-2{
            margin: 7px;
            text-align: center;
        }
        .heading-1{
            text-transform: uppercase;
            font-size: 14px;
        }
        .heading-2{
            margin: 7px;
            text-align: center;
            font-size: 12px;
        }
        .watermark,
        .time-generated{
            position: fixed;
            bottom: 1.9cm;
            right: 1cm;
            color: #000;
            font-size: 10px;
        } 
        .time-generated{
            bottom: 1.9cm;
            left: 1cm;
        }

        .group_order_no{
            margin-left: 20px;
        }
    </style>
</head>
<body>

    <header>
        <img src="{{ public_path('assets/img/pdf_images/header.png') }}" style="width: 92%; height: auto;"> 
    </header>

    <div class="heading">
        <h4 class="heading-1" style="text-align: center;">
            {{ config('meetings.quaterly_meetings.'.$meeting->quarter) }}    
            {{ $meeting->year }}
        </h4>
        <h4 class="heading-1" style="text-align: center;">
          
            @if ($meeting->getMeetingCouncilType() == 0)
                {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
            @elseif ($meeting->getMeetingCouncilType() == 1)
                {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
            @elseif ($meeting->getMeetingCouncilType() == 2)
                {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
            @endif 
        </h4> 
        <h5 class="heading-2">
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center ">
                <span class="text-muted fw-light text-center">{{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, l, h:i A') }}</span>

                @if ($meeting->modality == 1 || $meeting->modality == 3)
                <span> | Venue  at  {{$meeting->venue->name}}</span>
                @elseif ($meeting->modality == 2 || $meeting->modality == 3)
                <span> | Via {{ config('meetings.mode_if_online_types.'.$meeting->mode_if_online) }} - Online</span>
                @else
                    <span class="form-label m-0">Venue or platform not yet set</span>
                @endif
            </div>
        </h5>
        <h6 class="heading-1" style="margin-top: 20px;">ORDER OF BUSINESS</h6>
    </div>
    <h6 class="section-title">1. Preliminaries</h6>
        <p>{!! nl2br(e($orderOfBusiness->preliminaries)) !!}</p>

    
    <h6 class="section-title">2. New Business</h6>

    @php 
        $counter = 1; 
        $groupCounter = 1;
        $noProposals = collect($categorizedProposals)->flatten()->isEmpty();
        $allProposalIds = collect($categorizedProposals)->flatten()->pluck('id');
    @endphp
    @if ($noProposals)
        <div class="alert alert-warning" role="alert">
            <i class="bx bx-info-circle"></i> No new order of business available at the moment.
        </div>
    @else
        @foreach ($matters as $type => $title)
            @php 
                // Group proposals and standalone proposals together based on order_no
                $allProposals = collect();

                // Add standalone proposals to collection
                foreach ($categorizedProposals[$type]->whereNull('group_proposal_id') as $proposal) {
                    $allProposals->push([
                        'type' => 'individual',
                        'order_no' => $proposal->order_no,
                        'data' => $proposal
                    ]);
                }

                // Add grouped proposals to collection
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
            @endphp
           @if ($categorizedProposals[$type]->count() > 0)
                <div class=""><br>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="4" class="table-title" style="">{{ $title }}</th>
                            </tr>
                            <tr>
                                <th class="th-col">No.</th>
                                <th class="th-col">Title of the Proposal</th>
                                <th class="th-col" style="width: 120px;">Presenters</th>
                                <th class="th-col">Requested Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allProposals as $proposal)
                                @if ($proposal['type'] === 'individual')
                                    <tr>
                                        <td>2.<span class="order_no">{{ $counter }}</span></td>
                                        <td style="">
                                            <span>{{ $proposal['data']->proposal->title }}</span>
                                        </td>
                                        <td style="">
                                            <div class="">
                                                @if (isset($proposal['data']->proposal->proponents) && $proposal['data']->proposal->proponents->isNotEmpty())
                                                    @foreach ($proposal['data']->proposal->proponents as $proponent)
                                                        {{ $proponent->name }}@if (!$loop->last), @endif
                                                    @endforeach
                                                @else
                                                    <small class="text-muted">No presenters</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="" style="text-transform: none;">
                                                {{ config('proposals.requested_action.'.$proposal['data']->proposal->action) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @php $counter++; @endphp
                                @else
                                    <tr class="" data-id="{{ $proposal['data']->first()->proposal_group->id }}">
                                        <td class="tr-group">2.<span class="order_no">{{ $counter }}</span></td>
                                        <td colspan="4" class="tr-group">
                                            <strong>{{ $proposal['data']->first()->proposal_group->group_title ?? 'Group Proposal' }}</strong>
                                        </td>
                                    </tr>
                                    @foreach ($proposal['data'] as $groupedProposal)
                                        <tr>
                                            <td class="pe-1">
                                                <span class="group_order_no">2.{{ $counter }}.{{ $groupCounter }}</span>
                                            </td>
                                            <td style="">
                                                <span>{{ $groupedProposal->proposal->title }}</span>
                                            </td>
                                            <td style="">
                                                <div class="">
                                                    @if ($groupedProposal->proposal->proponents->isNotEmpty())
                                                        @foreach ($groupedProposal->proposal->proponents ?? [] as $proponent)
                                                            {{ $proponent->name }}
                                                        @endforeach
                                                    @else
                                                        <small class="text-muted">No presenters</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="" style="text-transform: none;">
                                                    {{ config('proposals.requested_action.'.$groupedProposal->proposal->action) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @php $groupCounter++; @endphp
                                    @endforeach
                                    @php $counter++; $groupCounter = 1; @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    @endif

      @if ($otherMattersProposals->isNotEmpty())
        <div class="table-responsive text-nowrap mb-4">
            <table class="table table-bordered">
                <thead>
                    <tr style="background-color: var(--bs-primary) !important; border-color: var(--bs-primary)  !important;">
                        <th colspan="5" class="p-4 text-white">{{ $otherMattersTitle }}</th>
                    </tr>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>Title of the Proposal</th>
                        <th style="width: 200px;">Presenter</th>
                        <th style="width: 150px;">Requested Action</th>
                        <th style="width: 100px;">File</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach ($otherMattersProposals as $otherMatter)
                        <tr>
                            <td>3.{{ $counter }}</td>
                            <td>
                                <div style="white-space: wrap;">
                                    <span style="color: #697A8D;">{{ $otherMatter->proposal->title }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-3">
                                    @foreach ($otherMatter->proposal->proponents ?? [] as $proponent)
                                        <div class="d-flex align-items-center gap-3">
                                            <img class="rounded-circle avatar-sm" src="{{ $proponent->image && trim($proponent->image) !== '' ? $proponent->image : asset('assets/img/avatars/default-avatar.jpg') }}" alt="Avatar">
                                            <span>{{ $proponent->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="d-flex gap-2 align-items-center">
                                    <i class='bx bx-up-arrow-circle text-{{ $actionColors[$otherMatter->proposal->action] ?? 'primary' }}'></i>
                                    {{ config('proposals.requested_action.'.$otherMatter->proposal->action) }}
                                </span>
                            </td>
                            <td>
                                @if ($otherMatter->proposal->files->isNotEmpty())
                                    <button class="btn btn-sm btn-secondary view-files d-flex gap-2" data-files="{{ json_encode($otherMatter->proposal->files) }}" data-title="{{ $otherMatter->proposal->title }}">
                                        <i class='bx bx-file'></i> VIEW FILES
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                        <i class='bx bx-file'></i> NO FILES
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @php $counter++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
            @else
                <p class="text-muted">No other matters recorded.</p>
            @endif
        </div>
        
    <small class="time-generated">Generated on: {{ now()->format('F d, Y h:i A') }}</small>
    <small class="watermark">Generated through Policy Management Information System</small>

    <footer>
        <hr>    
        <div style="position: fixed; left: 240px; top: 17px;">
            <img src="{{ public_path('assets/img/pdf_images/image3.png') }}" style="width: 42%; height: auto;">
        </div>
        <div style="position: fixed; right:-100px; top: 22px;">
            <img src="{{ public_path('assets/img/pdf_images/image4.png') }}" style="width: 55%; height: auto;">
        </div>
    </footer>

    <main>
        <div class="main page-break"></div>
    </main>
</body>
</html>
