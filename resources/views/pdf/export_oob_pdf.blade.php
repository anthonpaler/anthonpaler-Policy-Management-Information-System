<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>Order Of Business</title>
  <style>
    /* @font-face {
      font-family: 'Cambria';
      font-style: normal;
      font-weight: 400;
      src: url("{{ public_path('fonts/Cambria.ttf') }}") format('truetype');
    }
    @font-face {
      font-family: 'Cambria';
      font-style: bold;
      font-weight: 700;
      src: url("{{ public_path('fonts/CAMBRIAB.TTF') }}") format('truetype');
    } */

    header{
      position: fixed;
      top: -30px;
      width: calc(100% - 2.45cm);
      height: 2.5cm;
      /* border: 1px dashed red; */
      border-bottom: 1px solid black;
    }
    footer {
      position: fixed;
      bottom: -35px;
      width: calc(100% - 2.45cm);
      height: 2.5cm;
      /* border: 1px dashed red; */
      border-top: 1px solid black;
    }
    body {
      margin-top: 2.1cm;
      margin-left: 1.2cm;
      margin-right: 1.2cm;
      margin-bottom: 2.1cm;
      /* font-family: 'Cambria'; */
      /* border: 1px dashed blue; */
      page-break-before: auto;
    }

    .header-img-container {
      display: table;
      width: 100%;
      text-align: center;
      padding: 0;
    }
    .footer-img-container{
      display: table;
      width: 100%;
      margin-top: 20px;
      /* page-break-after: auto; */
    }
    .header-img-container .bagong_pilipinas_img{
      height: 79px;
      width: 75px;
      display: inline-block;
      vertical-align: middle;
      margin-left: 15px;
    }
    .header-img-container .slsu_logo_img{
      height: 72px;
      width: 290px;
      display: inline-block;
      vertical-align: middle;
      margin-right: 15px;
    }
    .footer-img-container .stars_rating_system_img{
      height: 65px;
      width: 170px;
      display: inline-block;
      vertical-align: middle;
      margin-left: 210px;
      margin-right: 95px;
    }

    .footer-img-container .socotec_img{
      height: 65px;
      width: 130px;
      display: inline-block;
      vertical-align: middle;
    }
    .bagong_pilipinas_img img,
    .slsu_logo_img img,
    .stars_rating_system_img img,
    .socotec_img img{
      height: 100%;
      width: 100%;
      object-fit: contain;
    }
    header small {
      font-size: 9px;
      display: table;
      width: 100%;
      text-align: center;
      /* font-family: 'Cambria'; */
    }
    .watermark,
    .time-generated{
      position: fixed;
      right: 1.25cm;
      color: #000;
      font-size: 10px;
      top: -17px;
      /* font-family: 'Cambria'; */
    }
    .time-generated{
      left: 1.25cm;
    }
    .heading-3,
    .heading-4{
      font-size: 13px;
      margin: 2px;
      font-weight:normal;
      text-align: center;
    }
    .heading-4{
      font-size: 12px;
    }
    .heading-2{
      font-size: 14px;
      margin: 2px;
      font-weight:bold;
      text-align: center;
      text-transform: uppercase;
    }
    .heading-1{
      font-size: 16px;
      margin: 2px;
      font-weight: bold;
      text-align: center;
      text-transform: uppercase;
      /* font-family: 'Cambria'; */
    }
    .section-title{
      font-size: 12px;
      font-weight: bold;
      margin: 0;
      margin-top: 20px;
    }
    .prelimary_content{
      font-size: 11px;
      margin: 0;
      padding-left: 30px;
      text-align: justify;
      text-justify: inter-word;
    }
    /* .table-container{
      margin-top: 10px;
    } */
    table {
      border-collapse: collapse;
      width: 100%;
      font-size: 11px;
      margin-top: 20px;
    }
    thead {
      display: table-header-group;
      page-break-inside: avoid;
    }
    th {
      background-color: #078EED;
      color: #FFFFFF;
      text-align: center;
      padding: 6px;
      border: .5px solid #AACFEA;
      font-weight: bold;
      font-size: 11px;
    }
    .table-header{
      background-color: #0071C1;
      font-size: 13px;
      text-transform: uppercase;
      padding: 7px;
    }
    .tr-group{
      background-color:rgb(191, 218, 248);
    }
    td {
      text-align: left;
      border: .5px solid #AACFEA;
      padding: 5px 10px;
    }
    .group-item{
      padding-left: 30px;
    }
  </style>
</head>
<body>
  <header>
    {{-- HEADER --}}
    <div class="header-img-container">
      <div class="slsu_logo_img">
        <img src="{{ public_path('assets/img/pdf_images/slsu_logo.png') }}">
      </div>
      <div class="bagong_pilipinas_img">
        <img src="{{ public_path('assets/img/pdf_images/bagong_pilipinas.png') }}">
      </div>
    </div>
    <small>Excellence | Service | Leadership and Good Governance | Innovation | Social Responsibility | Integrity | Professionalism | Spirituality</small>
  </header>
  {{-- END HEADER --}}

  {{-- FOOTER --}}
  <footer>
    <small class="time-generated">Generated on: {{ now()->format('F d, Y h:i A') }}</small>
    <small class="watermark">Generated through Policy Management Information System</small>

    <div class="footer-img-container">
      <div class="stars_rating_system_img">
        <img src="{{ public_path('assets/img/pdf_images/stars_rating_system.png') }}">
      </div>
      <div class="socotec_img">
        <img src="{{ public_path('assets/img/pdf_images/socotec.png') }}">
      </div>
    </div>
  </footer>
  {{-- END FOOTER --}}

  {{-- HEADING SECTION --}}
  <div class="heading">
    <h4 class="heading-3" style="text-transform: uppercase;">
        {{ config('meetings.quarterly_meetings.'.$meeting->quarter) }}
        {{ $meeting->year }}
    </h4>
    <h4 class="heading-2">
        @if ($meeting->getMeetingCouncilType() == 0)
            {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
        @elseif ($meeting->getMeetingCouncilType() == 1)
            {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
        @elseif ($meeting->getMeetingCouncilType() == 2)
            {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
        @endif
    </h4>
    <h5 class="heading-4">
      <span class="text-muted fw-light text-center">{{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, l, h:i A') }}</span>

      @if ($meeting->modality == 1 || $meeting->modality == 3)
      <span> | Venue  at  {{$meeting->venue}}</span>
      @elseif ($meeting->modality == 2 || $meeting->modality == 3)
      <span> | Via {{ config('meetings.mode_if_online_types.'.$meeting->mode_if_online) }} - Online</span>
      @else
          <span class="form-label m-0">Venue or platform not yet set</span>
      @endif
    </h5>
    <h6 class="heading-1" style="margin-top: 20px;">ORDER OF BUSINESS</h6>
  </div>
  {{-- END HEADING SECTION --}}

  {{-- PRELIMINARY --}}
  <h6 class="section-title">1. Preliminaries</h6>
  <div class="prelimary_content">{!! $orderOfBusiness->preliminaries !!}</div>
  {{-- END PRELIMINARY--}}

  {{-- NEW BUSINESS --}}
  <h6 class="section-title">2. New Business</h6>
  <div class="table-container">
    @php
      $counter = 1;
      $groupCounter = 1;
      $noProposals = collect($categorizedProposals)->flatten()->isEmpty();
      $allProposalIds = collect($categorizedProposals)->flatten()->pluck('id');
    @endphp
    @if ($noProposals)
      <p style="color: red; text-align: center;">No new order of business available at the moment.</p>
    @else
      @foreach ($matters as $type => $title)
        @php
            // Group proposals and standalone proposals together
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
                $groupOrderNo = $proposals->first()->proposal_group->order_no ?? 0;
                $allProposals->push([
                    'type' => 'group',
                    'order_no' => $groupOrderNo,
                    'group_id' => $groupID,
                    'data' => $proposals
                ]);
            }

            $allProposals = $allProposals->sortBy('order_no');
        @endphp
        @if ($categorizedProposals[$type]->count() > 0)
          <table>
            <thead>
              <tr>
                  <th colspan="4" class="table-header" style="">{{ $title }}</th>
              </tr>
              <tr>
                <th width="10%">No.</th>
                <th width="45%">Title of the Proposal</th>
                <th width="22%">Presenters</th>
                <th width="23%">Requested Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allProposals as $proposal)
                @if ($proposal['type'] === 'individual')
                  @php
                    $presenters = isset($proposal['data']->proposal->proponents) && $proposal['data']->proposal->proponents->isNotEmpty()
                                ? implode(', ', $proposal['data']->proposal->proponents->pluck('name')->toArray())
                                : '<span>No presenters</span>';

                    $requestedAction = config('proposals.requested_action.' . $proposal['data']->proposal->action) ?? 'N/A';
                  @endphp
                  <tr>
                    <td>2.{{$counter}}</td>
                    <td>
                      {!! str_replace('₱', "<span style=\"font-family: 'dejavu sans' !important;\">₱</span>", htmlspecialchars($proposal['data']->proposal->title, ENT_NOQUOTES, 'UTF-8')) !!}
                    </td>
                    <td>{{$presenters}}</td>
                    <td>{{$requestedAction}}</td>
                  </tr>
                @else
                  @php
                    $groupTitle = $proposal['data']->first()->proposal_group->group_title ?? 'Group Proposal';
                  @endphp
                  <tr class="tr-group">
                    <td width="10%">2.{{$counter}}</td>
                    <td colspan="3" width="90%">{{$groupTitle}}</td>
                  </tr>
                  @foreach ($proposal['data']->first()->proposal_group->files as $groupedAttachment)
                    <tr>
                      <td width="10%" class="group-item">2.{{ $counter }}.{{ $groupCounter }}</td>
                      <td colspan="3" width="90%">{{ $groupedAttachment->file_name }}</td>
                    </tr>
                    @php $groupCounter++; @endphp
                  @endforeach
                  @foreach ( $proposal['data'] as $groupedProposal )
                    @php
                       $presenters = $groupedProposal->proposal->proponents->isNotEmpty()
                                    ? implode(', ', $groupedProposal->proposal->proponents->pluck('name')->toArray())
                                    : '<span>No presenters</span>';

                      $requestedAction = config('proposals.requested_action.' . $groupedProposal->proposal->action) ?? 'N/A';
                    @endphp
                    <tr>
                      <td width="10%" class="group-item"><span>2.{{ $counter . '.' . $groupCounter}}</span></td>
                      <td>
                        {!! str_replace('₱', "<span style=\"font-family: 'dejavu sans' !important;\">₱</span>", e($groupedProposal->proposal->title)) !!}
                      </td>
                      <td>{{$presenters}}</td>
                      <td>{{$requestedAction}}</td>
                    </tr>
                    @php
                       $groupCounter++;
                    @endphp
                  @endforeach
                @endif
                @php $counter++;  $groupCounter = 1; @endphp
              @endforeach
            </tbody>
          </table>
        @endif
      @endforeach
    @endif
    @if ($otherMattersProposals->isNotEmpty())
      <h6 class="section-title">3. Other Matters</h6>
      <table>
        <thead>
          <tr>
              <th colspan="5" class="table-header" style="">{{ $otherMattersTitle }}</th>
          </tr>
          <tr>
            <th width="10%">No.</th>
            <th width="35%">Title of the Proposal</th>
            <th width="20%">Presenters</th>
            <th width="15%">Matter</th>
            <th width="20%">Requested Action</th>
          </tr>
        </thead>
        <tbody>
          @php
            $counter = 1;
          @endphp
          @foreach ( $otherMattersProposals as $otherMatter )
            @php
             $presenters = isset($otherMatter->proposal->proponents) && $otherMatter->proposal->proponents->isNotEmpty()
                ? implode(', ', $otherMatter->proposal->proponents->pluck('name')->toArray())
                : '<span>No presenters</span>';

              if(in_array($otherMatter->proposal->type,  [1,3,4])){
                $matter = config('proposals.matters.' . $otherMatter->proposal->type) ?? 'N/A';
              }elseif($otherMatter->proposal->type == 2){
                $matter = config('proposals.proposal_subtypes.' . $otherMatter->proposal->sub_type) ?? 'N/A';
              }
              $requestedAction = config('proposals.requested_action.' . $otherMatter->proposal->action) ?? 'N/A';
            @endphp
            <tr>
              <td>3.{{$counter}}</td>
              <td>
                {!! str_replace('₱', "<span style=\"font-family: 'dejavu sans' !important;\">₱</span>", e($otherMatter->proposal->title)) !!}
              </td>
              <td>{{$presenters}}</td>
              <td>{{ $matter}}</td>
              <td>{{$requestedAction}}</td>
            </tr>
            @php
              $counter++;
            @endphp
          @endforeach
        </tbody>
      </table>
    @endif

      <h6 class="section-title">4. Adjournment</h6>


  </div>
  {{-- END NEW BUSINESS --}}
</body>
</html>
