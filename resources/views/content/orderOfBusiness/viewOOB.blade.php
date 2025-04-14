@extends('layouts/contentNavbarLayout')

@section('title', 'Order of Business')

@section('content')
<div class="bread-crumbs overflow-auto" style="max-width: 100%; white-space: nowrap;">
    <h5>Dashboard</h5>
    <div class="divider"></div>
    <a href="/">
        <i class='bx bx-home-alt' ></i>
    </a>
    <i class='bx bx-chevron-right' ></i>
    <a href="{{ route(getUserRole().'.order-of-business')}}" >Order of Business</a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#" >Order of Business Information</a>
</div>

<!-- Modal Upload Previous Minutes -->
<div class="modal fade" id="previousMinModal" tabindex="-1" aria-labelledby="previousMinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">UPLOAD PREVIOUS MINUTES</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="">
                <form id="uploadMinutesForm" enctype="multipart/form-data">
                    @csrf
                    <label for="previous_minutes" class="form-label">Previous Minute File</label>
                    <input type="file" name="previous_minutes" id="previous_minutes" class="form-control" required>
                    <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
                <div id="uploadMessage" class="mt-2"></div>

            </div>
        </div>
    </div>
</div>
<div class="card p-4">
    <div class="mt-3 mb-3">
        <button type="button" id="exportOOB" class="btn btn-secondary d-flex gap-2 {{$orderOfBusiness->status == 0 ? 'd-none' : ''}}">
            <a href="{{ route('oob.export.pdf', ['level' => $orderOfBusiness->meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}" class="text-white d-flex align-items-center gap-2" target="_blank"><i class='bx bx-export'></i> Export OOB</a>
        </button>
    </div>

    @if (session('isSecretary'))
    <form action="{{ route(getUserRole().'.order_of_business.save', ['level' => $meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}" method="post" id="oobFrm" meeting-id="{{encrypt($orderOfBusiness->meeting->id)}}">
    @endif
        <div class="d-flex flex-column justify-content-center align-items-center">
            <h4 class="card-header p-0 mb-2 text-center">
                {{ config('meetings.quaterly_meetings.'.$meeting->quarter) }}
                @if ($meeting->getMeetingCouncilType() == 0)
                    {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                @elseif ($meeting->getMeetingCouncilType() == 1)
                    {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                @elseif ($meeting->getMeetingCouncilType() == 2)
                    {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
                @endif
                {{ $meeting->year }}
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center ">
                <span class="text-muted fw-light text-center">{{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, l, h:i A') }}</span>

                @if ($meeting->modality == 1 || $meeting->modality == 3)
                <span> | Venue  at  {{ $meeting->venue ?? 'Not Set' }}</span>
                @elseif ($meeting->modality == 2 || $meeting->modality == 3)
                <span> | Via {{ config('meetings.mode_if_online_types.'.$meeting->mode_if_online) }} - Online</span>

                <a href="{{$meeting->link ? $meeting->link : '/#'}}"  class="text-primary m-0"><i class='bx bx-link me-1'></i>Click Meeeting Link</a>
                @else
                    <span class="form-label m-0">Venue or platform not yet set</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 mt-3 mb-3">
                <h5 class="card-header p-0 ">ORDER OF BUSINESS</h5>
            </div>
        </div>

        <!-- Preliminaries Section -->
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-2 gap-2">
              <label class="form-label">1. Preliminaries</label>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        @if (session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
                            @if(empty($orderOfBusiness->previous_minutes))
                                <button class="btn btn-sm btn-primary d-flex align-items-center gap-2"
                                id="openMinutesModal">
                                    <i class='bx bx-upload'></i>
                                    Upload Previous Minutes
                                </button>
                            @endif
                            {{-- Show View Button only if there is a file --}}
                            {{-- EDIT FILE --}}
                            <button type="button" class="btn btn-sm btn-warning d-flex align-items-center gap-2" id="editMinutesButton" style="display: none;">
                                <i class='bx bx-edit'></i> Edit Previous Minutes File
                            </button>
                        @endif

                        @if(!empty($orderOfBusiness->previous_minutes))
                            <a href="{{ asset('storage/previous_minutes/' . $orderOfBusiness->previous_minutes) }}"
                                target="_blank"
                                class="btn btn-sm btn-success d-flex align-items-center gap-2"
                                id="viewButton">
                                <i class='bx bx-file'></i>
                                View Previous Minutes
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- <div class="input-group input-group-merge">
                <textarea
                    id="preliminaries"
                    class="form-control"
                    placeholder="Enter preliminaries."
                    aria-label="Enter preliminaries."
                    name="preliminaries"
                    rows="6"
                    @if(session('isProponent') ||(session('isSecretary') && session('secretary_level') != $meeting->getMeetingCouncilType()))
                        disabled
                    @endif
                >    {{$orderOfBusiness->preliminaries}}
                </textarea>

                @error('preliminaries')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                @enderror
            </div> --}}
        @if(session('isProponent') ||(session('isSecretary') && session('secretary_level') != $meeting->getMeetingCouncilType()))
          <div class="mt-1 mb-3 ms-4">
            {!! $orderOfBusiness->preliminaries !!}
          </div>
        @else
          <div class="mt-3 mb-3">
            <textarea  name="preliminaries" id="preliminaries">{{ $orderOfBusiness->preliminaries ?? '' }}</textarea>
          </div>
        @endif
        <!-- Modal -->
        <div class="modal fade" id="proposalFIleModal" tabindex="-1" aria-labelledby="proposalFIleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">PROPOSAL FILES</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalFiles">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New Business Section -->
        @if (session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
            <div class="stick-buttons-group">
                <div class="">
                    <button id="enableSelection"  class="action-btn primary">
                        <i class='bx bx-square'></i>
                        <span class="tooltiptext">Group Proposals</span>
                    </button>
                    <button id="cancelSelection" class="action-btn danger">
                        <i class='bx bx-revision' ></i>
                        <span class="tooltiptext">Cancel</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">2. New Business</label>

            @php
                $counter = 1;
                $groupCounter = 1;
                $actionColors = ['secondary', 'success', 'warning', 'info'];
                $noProposals = collect($categorizedProposals)->flatten()->isEmpty();
                $proposalKey = match ($meeting->getMeetingLevel()) {
                    'Local' => 'local_proposal_id',
                    'University' => 'university_proposal_id',
                    'BOR' => 'board_proposal_id',
                    default => ''
                };

                $otherMattersProposalIds = collect($otherMattersProposals)->pluck($proposalKey);
                $allProposalIds = collect($categorizedProposals)->flatten()->pluck($proposalKey);
                $allProposalIds = $allProposalIds->merge($otherMattersProposalIds);
            @endphp

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
                        $groupOrderNo = $proposals->first()->proposal_group->order_no;
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
                    <div class="table-responsive text-nowrap mb-4">
                        <table class="table table-bordered sortable" id="{{ session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()) ? 'oobTable' :  ''}}">
                            <thead>
                                <tr style="background-color: var(--bs-primary) !important; border-color: var(--bs-primary)  !important;">
                                    <th colspan="5" class="p-4 text-white">{{ $title }}</th>
                                </tr>
                                <tr>
                                    <th style="width: 50px;">No.</th>
                                    <th>Title of the Proposal</th>
                                    <th style="width: 200px;">Presenters</th>
                                    <th style="width: 150px;">Requested Action</th>
                                    <th style="width: 100px;">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allProposals as $proposal)
                                    @if ($proposal['type'] === 'individual')
                                        <tr class="selectable-row" data-group="false" data-id="{{ encrypt($proposal['data']->proposal->id) }}">
                                            <td>2.<span class="order_no">{{ $counter }}</span></td>
                                            <td>
                                                <div style="white-space: wrap;">
                                                    <span style="color: #697A8D;">{{ $proposal['data']->proposal->title }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-3">
                                                    @foreach ($proposal['data']->proposal->proponents ?? [] as $proponent)
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img class="rounded-circle avatar-sm" src="{{ $proponent->image && trim($proponent->image) !== '' ? $proponent->image : asset('assets/img/avatars/default-avatar.jpg') }}" alt="Avatar">
                                                            <span>{{ $proponent->name }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <span class="d-flex gap-2 align-items-center">
                                                    <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal['data']->proposal->action] ?? 'primary' }}'></i>
                                                    {{ config('proposals.requested_action.'.$proposal['data']->proposal->action) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($proposal['data']->proposal->files->isNotEmpty())
                                                    <button class="btn btn-sm btn-secondary view-files d-flex gap-2" data-files="{{ json_encode($proposal['data']->proposal->files) }}" data-title="{{ $proposal['data']->proposal->title }}">
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
                                    @else
                                        <tr class="tr-group selectable-row group position-relative" data-group="true" data-id="{{encrypt($proposal['data']->first()->proposal_group->id )}}">
                                          <td>2.<span class="order_no">{{ $counter }}</span></td>
                                          <td colspan="4">
                                            <strong>{{ $proposal['data']->first()->proposal_group->group_title ?? 'Group Proposal' }}</strong>
                                            @if (session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
                                                <div class="dropdown position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 1000;">
                                                    <button class="btn btn-sm btn-warning dropdown-toggle d-none group-menu-btn" type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><button class="dropdown-item ungroup-btn">Ungroup</button></li>
                                                        <li><button class="dropdown-item edit-group-btn">Edit Group</button></li>
                                                        <li><button class="dropdown-item add-group-files-btn">Add Attachments</button></li>
                                                    </ul>
                                                </div>
                                            @endif
                                          </td>
                                        </tr>
                                        @foreach ($proposal['data']->first()->proposal_group->files as $groupedAttachment)
                                          <tr class="group-items selectable-row tr-group position-relative"  data-file-name="{{ $groupedAttachment->file_name }}" data-file="{{ $groupedAttachment->file }}" data-id="{{ encrypt($groupedAttachment->id) }}"  data-group-attachment="true">
                                            <td class="ps-5 pe-1">
                                              <span class="g_order_no">2.{{ $counter }}.{{ $groupCounter }}</span>
                                            </td>
                                            <td colspan="3">
                                              <div style="white-space: wrap;">
                                                  <span style="color: #697A8D;">{{ $groupedAttachment->file_name }}</span>
                                              </div>
                                            </td>
                                            <td>
                                              @if ($groupedAttachment->file)
                                                  <button class="btn btn-sm btn-secondary view-single-file-preview d-flex gap-2" data-file-url="/storage/proposals/{{$groupedAttachment->file}}">
                                                      <i class='bx bx-file'></i> VIEW FILES
                                                  </button>
                                              @else
                                                  <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                                      <i class='bx bx-file'></i> NO FILES
                                                  </button>
                                              @endif
                                              @if (session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
                                                <div class="dropdown position-absolute" style="left: 5px; top: 50%; transform: translateY(-50%); z-index: 1000;">
                                                    <button class="btn btn-sm btn-warning dropdown-toggle d-none group-menu-btn" type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><button class="dropdown-item edit-group-file-btn">Edit Attachment</button></li>
                                                        <li><button class="dropdown-item delete-group-file-btn">Delete Attachment</button></li>
                                                    </ul>
                                                </div>
                                              @endif
                                            </td>
                                          </tr>
                                          @php $groupCounter++; @endphp
                                        @endforeach
                                        @foreach ($proposal['data'] as $groupedProposal)
                                            <tr class="selectable-row group-items" data-group="false" data-id="{{ encrypt($groupedProposal->proposal->id) }}">
                                                <td class="ps-5 pe-1">
                                                    <span class="g_order_no">2.{{ $counter }}.{{ $groupCounter }}</span>
                                                </td>
                                                <td>
                                                    <div style="white-space: wrap;">
                                                        <span style="color: #697A8D;">{{ $groupedProposal->proposal->title }}</span>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="d-flex flex-column gap-3">
                                                        @foreach ($groupedProposal->proposal->proponents ?? [] as $proponent)
                                                            <div class="d-flex align-items-center gap-3">
                                                                <img class="rounded-circle avatar-sm" src="{{ $proponent->image && trim($proponent->image) !== '' ? $proponent->image : asset('assets/img/avatars/default-avatar.jpg') }}" alt="Avatar">
                                                                <span>{{ $proponent->name }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="d-flex gap-2 align-items-center">
                                                        <i class='bx bx-up-arrow-circle text-{{ $actionColors[$groupedProposal->proposal->action] ?? 'primary' }}'></i>
                                                        {{ config('proposals.requested_action.'.$groupedProposal->proposal->action) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($groupedProposal->proposal->files->isNotEmpty())
                                                        <button class="btn btn-sm btn-secondary view-files d-flex gap-2" data-files="{{ json_encode($groupedProposal->proposal->files) }}" data-title="{{ $groupedProposal->proposal->title  }}">
                                                            <i class='bx bx-file'></i> VIEW FILES
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                                            <i class='bx bx-file'></i> NO FILES
                                                        </button>
                                                    @endif
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

            <script>
                var postedToAgendaProposalIDS = @json($allProposalIds);
            </script>
    </div>
    <div class="mb-3">
      <div class="d-flex align-items-center gap-2 mb-2">
          <label class="form-label m-0">3. Other Matters</label>
            @if (session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
          <button id="addOtherMatterBtn" class="btn btn-primary btn-xs m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Other Matter">
              <i class='bx bx-plus'></i>
          </button>
          @endif
      </div>
      @if ($otherMattersProposals->isNotEmpty())
        <div class="table-responsive text-nowrap mb-4">
            <table class="table table-bordered sortable" id="{{ session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()) ? 'oobOtherMatterTable' :  ''}}">
              <thead>
                  <tr style="background-color: var(--bs-primary) !important; border-color: var(--bs-primary)  !important;">
                      <th colspan="6" class="p-4 text-white">{{ $otherMattersTitle }}</th>
                  </tr>
                  <tr>
                      <th style="width: 50px;">No.</th>
                      <th>Title of the Proposal</th>
                      <th style="width: 200px;">Presenter</th>
                      <th style="width: 100px;">Matter</th>
                      <th style="width: 100px;">Requested Action</th>
                      <th style="width: 100px;">File</th>
                  </tr>
              </thead>
              <tbody>
                  @php $counter = 1; @endphp
                  @foreach ($otherMattersProposals as $otherMatter)
                      @php
                         if(in_array($otherMatter->proposal->type,  [1,3,4])){
                            $matter = config('proposals.matters.' . $otherMatter->proposal->type) ?? 'N/A';
                          }elseif($otherMatter->proposal->type == 2){
                            $matter = config('proposals.proposal_subtypes.' . $otherMatter->proposal->sub_type) ?? 'N/A';
                          }
                      @endphp
                      <tr class="" data-id="{{ encrypt($otherMatter->proposal->id) }}">
                          <td>3.<span class="order_no">{{ $counter }}</span></td>
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
                          <td>{{$matter}}</td>
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
          {{-- <p class="text-muted">No other matters recorded.</p> --}}
      @endif
    </div>


        @if(session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
            <div class="d-flex gap-3 align-items-center flex-wrap mt-4">
                <button type="submit" class="btn btn-primary d-flex gap-2" id="saveOOBBtn">
                    <i class='bx bx-save' ></i>
                    <span class="text-nowrap">Save Changes</span>
                </button>
                <button type="buttton" class="btn btn-success d-flex gap-2" id="disseminateOOBBtn" data-id="{{encrypt($orderOfBusiness->id)}}" data-action = "{{ route(getUserRole().'.dissemenate.order_of_business', ['level' => $meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}">
                    <i class='bx bx-send'></i>
                    <span class="text-nowrap">{{$orderOfBusiness->status == 1 ? 'Redisseminate OOB' : 'Disseminate OOB'}}</span>
                </button>
            </div>
        @endif
    @if (session('isSecretary'))
    </form>
    @endif

    @if(session('isProponent') ||(session('isSecretary') && session('secretary_level') != $meeting->getMeetingCouncilType()))
    @else
    <!-- ADD OTHER MATTERS MODAL -->
    <div class="modal fade" id="otherMattersModal" tabindex="-1" aria-labelledby="otherMattersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary" id="otherMattersModalLabel">Add Other Matters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route(getUserRole().'.addOtherMatters', ['meeting_id' => encrypt($meeting->id)]) }}" enctype="multipart/form-data" id="otherMattersFrm">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="title">Title <span class="ms-1 text-danger">*</span></label>
                            <textarea id="title" name="title" class="form-control" placeholder="Enter title" required rows="3"></textarea>
                        </div>

                        <!-- Proponent or Presenter Email -->
                        <div class="mb-3">
                            <label class="form-label" for="proponent_email">Proponent<span class="ms-1 text-danger">*</span></label>
                            <div class="input-group">
                                <span id="email-icon" class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input
                                    type="text"
                                    id="proponent_email_matter"
                                    name="proponent_email"
                                    class="form-control @error('proponent_email') is-invalid @enderror"
                                    placeholder="Enter proponent's email"
                                    required
                                >
                            </div>
                            @error('proponent_email')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type of Matter -->
                        <div class="mb-3">
                            <label class="form-label" for="matter">Type of Matter or Proposal <span class="ms-1 text-danger">*</span></label>
                            <div class="input-group">
                                <span id="matters-icon" class="input-group-text"><i class="bx bx-briefcase"></i></span>
                                <select class="form-select @error('matter') is-invalid @enderror" id="matter" name="matter" required>
                                    <option value="" disabled>Select Type of Matter or Proposal</option>
                                    @switch(session('user_role'))
                                        @case(0)
                                            <option value="1">{{ config('proposals.matters.1') }}</option>
                                            @break
                                        @case(1)
                                            <option value="2">{{ config('proposals.matters.2') }}</option>
                                            @break
                                        @default
                                            @foreach (config('proposals.matters') as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                    @endswitch
                                </select>
                            </div>
                                @error('matter')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                        </div>

                    <!-- Requested Action -->
                    <div class="mb-3">
                            <label class="form-label" for="action">Requested Action <span class="ms-1 text-danger">*</span></label>
                            <div class="input-group">
                                <span id="action-icon" class="input-group-text"><i class="bx bx-task"></i></span>
                                <select class="form-control @error('action') is-invalid @enderror" id="action" name="action">
                                    @switch(session('user_role'))
                                        @case(0)
                                            <option value="1">{{ config('proposals.requested_action.1') }}</option>
                                            <option value="3">{{ config('proposals.requested_action.3') }}</option>
                                            @break
                                        @case(1)
                                            <option value="2">{{ config('proposals.requested_action.2') }}</option>
                                            <option value="3">{{ config('proposals.requested_action.3') }}</option>
                                            @break
                                        @default
                                            @foreach (config('proposals.requested_action') as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                    @endswitch
                                </select>
                            </div>
                            @error('action')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                    </div>

                    <!-- Sub Type -->
                    <div class="mb-3" id="subTypeContainer" style="display: none;">
                        <label class="form-label" for="sub_type">Sub Type</label>
                        <div class="input-group">
                            <span id="sub-type-icon" class="input-group-text"><i class="bx bx-category-alt"></i></span>
                            <select name="sub_type" id="sub_type" class="form-control @error('sub_type') is-invalid @enderror" required>
                                <option value="">Select Sub-type</option>
                                @foreach (config('proposals.proposal_subtypes') as $key => $subType)
                                    <option value="{{ $key }}" @selected(old('sub_type') == $key)>{{ $subType }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('sub_type')
                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Proposal Files -->
                    <div class="">
                        <h6 class="text-primary">PROPOSAL FILES</h6>
                        <div class="upload-container mb-3">
                            <label class="form-label" for="fileUpload">Proposal File/s <span class="ms-1 text-danger">*</span></label>
                            <div id="dropArea" class="drop-area">
                                <span class="upload-text">Drag & Drop files here, or <strong class="text-primary">click to upload</strong></span>
                                <small class="text-muted">Accepted formats: .pdf, .xls, .xlsx, and .csv only</small>
                                <input type="file" id="fileUpload" name="proposal_files[]" accept=".pdf,.xls,.xlsx,.csv" multiple hidden>
                            </div>
                            <h5 id="uploadedFilesLabel" class="file-header mt-3"><i class='bx bx-file'></i> Uploaded Files</h5>
                            <ul id="fileList" class="file-list mt-3">
                            </ul>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" id="addMatter" class="btn btn-primary">Add Other Matters</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Preview File -->
    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="height: 95%; display: flex; align-items: center;">
            <div class="modal-content" style="height: 100%;">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="modal-title" id="fileModalLabel">File Preview</h5>
                        <div class="d-flex align-items-center gap-3">
                            <i class="bx bx-fullscreen full-screen-file-preview" id="toggleIframeFullscreen"></i>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="flex-grow: 1; overflow: hidden;">
                    <iframe id="fileIframe" src="" width="100%" height="100%" style="height: 100%;" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE/EDIT GROUP MODAL -->
    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md">
          <div class="modal-content">
              <form method="POST" action="{{ route('save_proposal_group', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}" id="groupFrm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="groupModalLabel">Group Selected Proposals</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="">
                      <label class="form-label" for="">Enter Group Proposal Title<span class="ms-1 text-danger">*</span></label>
                      <div class="input-group input-group-merge">
                          <textarea name="group_title" id="group_title" rows="4" placeholder="Enter group proposal title" class="form-control"></textarea>
                      </div>
                  </div>
                  <input type="hidden" id="group_id" name="group_id" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveGroup" class="btn btn-primary">Save Group</button>
                    <button type="button" id="updateGroup" class="btn btn-primary" style="display: none;">Save Changes</button>
                </div>
              </form>
          </div>
      </div>
    </div>

    <!-- Modal Add Group Attachment-->
    <div class="modal fade" id="groupAttachmentModal" tabindex="-1" aria-labelledby="groupAttachmentModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
              <div class="d-flex align-items-center gap-3">
                  <h5 class="modal-title" id="groupAttachmentModalLabel">Proposal Group Attachment</h5>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" enctype="multipart/form-data" action="{{route('proposal.group-proposal.add-attachment')}}" id="groupAttachmentFrm" data-id="">
            @csrf
            <div class="modal-body">
              <div class="mb-2 d-flex gap-2 flex-wrap">
                <label class="form-label" for="">Group Title: </label>
                <h6 class="m-0 group_title"></h6>
              </div>
              <div class="mb-3">
                <label class="form-label" for="">File Name<span class="ms-1 text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <span  class="input-group-text">
                    <i class='bx bx-file' ></i>
                  </span>
                  <input type="text" class="form-control" placeholder="Enter file name" name="file_name">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="">Attach File<span class="ms-1 text-danger">*</span></label>
                <input type="file" class="form-control" name="file">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="addGroupAttachBtn">Add Attachment</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    {{-- Modal Edit Group Proposal --}}
    <div class="modal fade" id="groupEditAttachmentModal" tabindex="-1" aria-labelledby="groupEditAttachmentModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
              <div class="d-flex align-items-center gap-3">
                  <h5 class="modal-title" id="groupEditAttachmentModalLabel">Proposal Group Attachment</h5>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" enctype="multipart/form-data" action="{{route('proposal.group-proposal.edit-attachment')}}" id="groupEditAttachmentFrm" data-id="">
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label" for="">File Name<span class="ms-1 text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <span  class="input-group-text">
                    <i class='bx bx-file' ></i>
                  </span>
                  <input type="text" class="form-control file_name" placeholder="Enter file name" name="file_name" value="">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="">Attached File</label>
                <a class="form-control d-flex align-items-center gap-2" style="text-transform: none;" >
                  <i class='bx bx-file-blank'></i><span class="file"></span>
                </a>
              </div>
              <div class="mb-3">
                <label class="form-label" for="">Attach New File<span class="ms-1 text-muted">(Optional)</span></label>
                <input type="file" class="form-control" name="file">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="editGroupProposalBtn">Save Changes</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- For Jodit Text Editor --}}
<script
type="text/javascript"
src="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.js"
></script>
<script>
    $('#matter').on('change', function() {
        var matter = $(this).val();
        var subType = $('#sub_type');
        var actionSelect = $('#action');

        actionSelect.empty();

        if (matter == 1) {
            actionSelect.append(`
                @if (session('user_role') == 3)
                    <option value="4">Endorsement for Local ACAD</option>
                    <option value="1">Endorsement for UACAD</option>
                @endif
                @if (session('user_role') == 4)
                    <option value="6">Approval for UACAD</option>
                    <option value="3">Endorsement for BOR</option>
                @endif
                @if (session('user_role') == 5)
                    <option value="8">BOR Approval</option>
                @endif
            `);
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');
        } else if (matter == 2) {
            subType.prop('disabled', false);
            $('#subTypeContainer').css('display', 'block');

            actionSelect.append(`
                @if (session('user_role') == 3)
                    <option value="5">Endorsement for Local ADCO</option>
                    <option value="2">Endorsement for UADCO</option>
                @endif
                @if (session('user_role') == 4)
                    <option value="7">Approval for UADCO</option>
                    <option value="3">Endorsement for BOR</option>
                @endif
                @if (session('user_role') == 5)
                    <option value="8">BOR Approval</option>
                @endif
            `);
        }else if (matter == 3) {
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');

            actionSelect.append(`
                <option value="3">Endorsement for BOR</option>
                <option value="9">BOR Confirmation</option>
            `);
        }
        else if (matter == 4) {
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');

            actionSelect.append(`
                <option value="3">Endorsement for BOR</option>
                <option value="10">BOR Information</option>
            `);
        }
    });
    // For Jodit Editor in Pre
    Jodit.make('#preliminaries');

    document.addEventListener("DOMContentLoaded", function () {
        let emailInput = document.getElementById("proponent_email_matter");
        let tagify = new Tagify(emailInput, {
            enforceWhitelist: false,
            maxTags: 1,
            whitelist: [],
            dropdown: {
                maxItems: 10,   // Show up to 10 results
                enabled: 1,     // Show dropdown on input
                closeOnSelect: false
            }
        });

        document.querySelector("#otherMattersFrm").addEventListener("submit", function (e) {
        let tagifiedEmails = tagify.value.map(tag => tag.value);
        let emailValue = tagifiedEmails[0] || "";

        // Validate email format before submitting
        if (!emailValue.match(/^[\w\.-]+@[\w\.-]+\.\w+$/)) {
            e.preventDefault(); // Prevent form submission
            toastr.error("Please enter a valid email address.");
            return;
        }

        emailInput.value = emailValue;
    });

        // Fetch proponent emails dynamically
        function fetchProponents(query) {
            $.ajax({
                url: "{{route('fetchProponents')}}",
                type: "GET",
                data: { search: query },
                success: function (response) {
                    tagify.settings.whitelist = response;
                    tagify.dropdown.show(); // Show dropdown
                }
            });
        }

        // Listen for input event to fetch data
        tagify.on("input", function (e) {
            let value = e.detail.value;
            if (value.length >= 2) { // Fetch only if at least 2 characters are typed
                fetchProponents(value);
            }
        });
    });

    var proposalStatus = @json(config('proposals.status'));
    $(document).ready(function () {

        // Open Add Other Matter Modal
        $("#addOtherMatterBtn").on("click", function (e) {
            e.preventDefault();
            $("#otherMattersModal").modal("show");
        });

        $("#otherMattersFrm").on("submit", function (e) {
            e.preventDefault(); // Prevent default form submission

            let formData = new FormData(this);
            let submitButton = $("#addMatter");
            submitButton.prop("disabled", true); // Disable button to prevent duplicate submissions

            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                submitButton.prop("disabled", true); // Disable button to prevent duplicate submissions
                submitButton.text("Adding..."); // Optionally change button text
            },
                success: function (response) {
                    if (response.type === "success") {
                        toastr.success(response.message, "Success");
                        $("#otherMattersModal").modal("hide"); // Close the modal
                        $("#otherMattersFrm")[0].reset(); // Reset the form

                        // Optionally refresh the list of Other Matters without reloading
                        loadOtherMatters();
                    } else {
                        toastr.error(response.message, "Error");
                    }
                },
                error: function (xhr) {
                    let response = xhr.responseJSON;
                    if (response && response.message) {
                        toastr.error(response.message, "Error");
                    } else {
                        toastr.error("An unexpected error occurred.", "Error");
                    }
                },
                complete: function () {
                    submitButton.prop("disabled", false); // Re-enable button
                }
            });
        });



        // Open Upload Modal
        $("#openMinutesModal").click(function(e) {
            e.preventDefault();
            $("#previousMinModal").modal("show");
        });

        // Upload Previous Minutes Form Submission
        $('#uploadMinutesForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route(getUserRole().'.upload.minutes') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    toastr.info("Uploading previous minutes...", "", { timeOut: 0, extendedTimeOut: 0 });
                },
                success: function(response) {
                    toastr.clear();
                    if (response.success) {
                        toastr.success(response.message);

                        $('#previousMinModal').modal('hide');
                        $("#uploadMinutesForm")[0].reset();

                        // Refresh the minutes view without reloading the page
                        checkPreviousMinutes();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    let errorMessage = "An error occurred. Please try again.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                }
            });
        });

        // Check if previous minutes exist and update buttons accordingly
        function checkPreviousMinutes() {
            let meetingId = $("input[name='meeting_id']").val();

            $.ajax({
                url: "{{ route('get.previous.minutes', ':meeting_id') }}".replace(':meeting_id', meetingId),
                type: "GET",
                success: function(response) {
                    if (response.success && response.previous_minutes) {
                        console.log("Previous Minutes Found:", response.previous_minutes);

                        // Update View Button
                        $("#viewButton")
                            .attr("href", "/storage/previous_minutes/" + response.previous_minutes)
                            .show();

                        // Show Edit Button
                        $("#editMinutesButton").show();

                        // Hide Upload Button
                        $("#openMinutesModal").hide();

                    } else {
                        console.log("No Previous Minutes Found");

                        // Show Upload Button & hide others
                        $("#openMinutesModal").show();
                        $("#viewButton").hide();
                        $("#editMinutesButton").hide();
                    }
                },
                error: function() {
                    console.error("Failed to fetch previous minutes.");
                }
            });
        }

        // Edit Previous Minutes
        $("#editMinutesButton").click(function(e) {
            e.preventDefault();
            $("#editMinutesModal").modal("show");
        });

        $('#editMinutesForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route(getUserRole().'.upload.minutes') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    toastr.info("Uploading new file...", "", { timeOut: 0, extendedTimeOut: 0 });
                },
                success: function(response) {
                    toastr.clear();
                    if (response.success) {
                        toastr.success(response.message);

                        $('#editMinutesModal').modal('hide');
                        $("#editMinutesForm")[0].reset();

                        checkPreviousMinutes();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(jqXHR) {
                    console.error(jqXHR);
                }
            });
        });

        // Initialize Check on Page Load
        checkPreviousMinutes();

        $(document).on("contextmenu", ".tr-group", function (event) {
          event.preventDefault();

          // Hide all other dropdowns and buttons
          $(".group-menu-btn").addClass("d-none");
          $(".dropdown-menu").addClass("d-none");

          // Get the button and dropdown for the clicked row
          let $dropdownBtn = $(this).find(".group-menu-btn");
          let $dropdownMenu = $(this).find(".dropdown-menu");

           $dropdownBtn.removeClass("d-none");
          $dropdownMenu.removeClass("d-none");

          $dropdownBtn.removeClass("d-none").dropdown("toggle");
      });


        // Hide menu when clicking elsewhere
        $(document).on("click", function () {
            $(".group-menu-btn").addClass("d-none");
        });

        // Handle 'Ungroup' click
        $(".ungroup-btn").click(function (e) {
            e.preventDefault();
            let groupId = $(this).closest(".tr-group").data("id");

            $.ajax({
                url: "{{ route('ungroup_proposal', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}",
                method: "POST",
                data: {
                    group_id: groupId,
                    _token: "{{ csrf_token() }}" // CSRF token for security
                },
                success: function (response) {
                    if (response.type === 'success') {
                        showAlert(response.type, response.title, response.message);
                        location.reload(); // Refresh to reflect changes
                    } else {
                        showAlert('danger', 'Error', response.message);
                    }
                },
                error: function (xhr) {
                    showAlert('danger', 'Error', (xhr.responseJSON ? xhr.responseJSON.error : "Unknown error"));
                }
            });
        });

        $(".edit-group-btn").click(function (e) {
            e.preventDefault();
            let groupId = $(this).closest(".tr-group").data("id");
            let groupTitle = $(this).closest(".tr-group").find("strong").text();
            // let orderNo = $(this).closest(".tr-group").find(".order_no").text();

            // Set the values in the modal
            $("#groupModal #group_title").val(groupTitle);
            // $("#groupModal #orderNo").val(orderNo);
            $("#groupModal #group_id").val(groupId);
            $("#groupModal #saveGroup").hide();
            $("#groupModal #updateGroup").show();

            // Show the modal
            let modal = new bootstrap.Modal($("#groupModal")[0]);
            modal.show();
        });

        $(".add-group-files-btn").on('click', function(event){
          event.preventDefault();
          let groupId = $(this).closest(".tr-group").data("id");
          let groupTitle = $(this).closest(".tr-group").find("strong").text();

          $("#groupAttachmentModal .group_title").text(groupTitle);
          $("#groupAttachmentFrm").attr("data-id", groupId);

          let modal = new bootstrap.Modal($("#groupAttachmentModal")[0])
          modal.show();
        });

        $(".edit-group-file-btn").on('click', function(event){
          event.preventDefault();
          let groupAttachmentID = $(this).closest(".tr-group").data("id");
          let fileName = $(this).closest(".tr-group").data("file-name");
          let file = $(this).closest(".tr-group").data("file");

          $("#groupEditAttachmentModal .file_name").val(fileName);
          $("#groupEditAttachmentModal .file").text(file);
          $("#groupEditAttachmentFrm").attr("data-id", groupAttachmentID);

          let modal = new bootstrap.Modal($("#groupEditAttachmentModal")[0])
          modal.show();
        });
        $(".delete-group-file-btn").on("click", function (e) {
          e.preventDefault();
          let groupAttachmentID = $(this).closest(".tr-group").data("id");
          var button = $(this);
          // alert(groupAttachmentID);
          Swal.fire({
              title: "Are you sure?",
              text: "You won't be able to revert this!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Yes, delete it!",
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                method: "POST",
                url: "{{route('proposal.group-proposal.delete-attachment')}}",
                data: {groupAttachmentID: groupAttachmentID},
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                  if(response.type == "success"){
                    showAlert(response.type, response.title, response.message);
                    button.closest("tr").remove();
                  }
                },
                error: function (xhr, status, error) {
                  console.log(xhr.responseText);
                  let response = JSON.parse(xhr.responseText);
                  showAlert("warning", response.title, response.message);
                }
              });
            }
          });
        });
        // ADD GROUP PROPOSAL ATTACHMENT
        $("#addGroupAttachBtn").on('click', function(event) {
          event.preventDefault();

          var groupAttachmentFrm = $("#groupAttachmentFrm");
          let groupId = groupAttachmentFrm.data('id');
          var actionUrl = groupAttachmentFrm.attr('action');

          let formData = new FormData(groupAttachmentFrm[0]);
          formData.append('group_proposal_id', groupId);

          $.ajax({
              method: "POST",
              url: actionUrl,
              data: formData,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              processData: false,
              contentType: false,
              beforeSend: function () {
                  $("#addGroupAttachBtn").text("Adding Attachment...").prop('disabled', true);
              },
              success: function (response) {
                  $("#addGroupAttachBtn").text("Add Attachment").prop('disabled', false);
                  if (response.type === 'success') {
                      groupAttachmentFrm[0].reset();
                      location.reload();
                  }
                  showAlert(response.type, response.title, response.message);
              },
              error: function (xhr, status, error) {
                  $("#addGroupProposalBtn").text("Add Attachment").prop('disabled', false);
                  console.log(xhr.responseText);
                  let response = JSON.parse(xhr.responseText);
                  showAlert("danger", response.title, response.message);
              }
          });
        });

        // EDIT GROUP PROPOSAL ATTACHMENT
        $("#editGroupProposalBtn").on('click', function(event) {
          event.preventDefault();

          var groupAttachmentFrm = $("#groupEditAttachmentFrm");
          let groupAttachmenID = groupAttachmentFrm.data('id');
          var actionUrl = groupAttachmentFrm.attr('action');

          let formData = new FormData(groupAttachmentFrm[0]);
          formData.append('group_attachment_id', groupAttachmenID);

          $.ajax({
              method: "POST",
              url: actionUrl,
              data: formData,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              processData: false,
              contentType: false,
              beforeSend: function () {
                  $("#editGroupProposalBtn").text("Saving Changes...").prop('disabled', true);
              },
              success: function (response) {
                  $("#editGroupProposalBtn").text("Save Changes").prop('disabled', false);
                  if (response.type === 'success') {
                      groupAttachmentFrm[0].reset();
                      location.reload();
                  }
                  showAlert(response.type, response.title, response.message);
              },
              error: function (xhr, status, error) {
                  $("#editGroupProposalBtn").text("Save Changes").prop('disabled', false);
                  console.log(xhr.responseText);
                  let response = JSON.parse(xhr.responseText);
                  showAlert("danger", response.title, response.message);
              }
          });
        });
        // Initialize Sortable.js
        $("#oobTable tbody").each(function () {
          $tableTr =  "#oobTable tbody tr";
          new Sortable(this, {
              animation: 150,
              handle: "tr",
              ghostClass: "sortable-ghost",
              onEnd: function (evt) {
                  updateOrder($tableTr);
              }
          });
        });

        $("#oobOtherMatterTable tbody").each(function () {
          $OtherMatterTableTr =  "#oobOtherMatterTable tbody tr";
          new Sortable(this, {
              animation: 150,
              handle: "tr",
              ghostClass: "sortable-ghost",
              onEnd: function (evt) {
                  updateOrder($OtherMatterTableTr);
              }
          });
        });

        function updateOrder($tableTr) {
          let order = [];
          let position = 1;

          $($tableTr).each(function (index) {
              let proposalId = $(this).data("id");
              let isGroup = $(this).data("group") === true;
              let isGroupAttachment = $(this).data("group-attachment") === true;

              order.push({ id: proposalId, isGroup: isGroup, order: index + 1, position: position, isGroupAttachment : isGroupAttachment });
              $(this).find("td:first .order_no").text(position);

              if ($(this).find(".order_no").length) {
                position++;
              }
          });

          console.log("Ordered Proposals: ");
          console.log(order);

          $.ajax({
              url: "{{ route('update_proposal_order', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}",
              method: "POST",
              data: {
                  orderData: order,
                  _token: "{{ csrf_token() }}"
              },
              success: function (response) {
                  console.log("Order updated successfully!", response);
              },
              error: function (xhr, status, error) {
                  console.error("Error updating order:", error);
              }
          });
        }

        let selectionEnabled = false;
        let selectedProposalRows = [];

        // Enable Selection
        $("#enableSelection").click(function (e) {
            e.preventDefault();
            selectionEnabled = true;
            $("#enableSelection, #cancelSelection").addClass("active");
        });

        // Cancel Selection
        $("#cancelSelection").click(function (e) {
            e.preventDefault();
            selectionEnabled = false;
            selectedProposalRows = [];
            $(".selectable-row").removeClass("selected-row");
            $("#enableSelection, #cancelSelection").removeClass("active");
        });

        // Row Click Selection
        $(".selectable-row").click(function (e) {
            if (!selectionEnabled) return;

            let rowId = $(this).attr("data-id");

            let isGroup = $(this).data("group") === true;
            if(isGroup ==  true){
                showAlert('danger', 'Invalid Row', 'Selection of a Group Proposal is not allowed');
                return;
            }

            if (selectedProposalRows.includes(rowId)) {
                selectedProposalRows = selectedProposalRows.filter(id => id !== rowId);
                $(this).removeClass("selected-row");
            } else {
                selectedProposalRows.push(rowId);
                $(this).addClass("selected-row");
            }
            console.log(selectedProposalRows);
        });

        // Right-Click to Open Modal
        $(".selectable-row").contextmenu(function (e) {
            if (!selectionEnabled || selectedProposalRows.length === 0) return;

            e.preventDefault();
            let modal = new bootstrap.Modal($("#groupModal")[0]);
            modal.show();
        });

        // Save Group
        $("#saveGroup").click(function () {
            let groupFrm = $("#groupFrm");
            var actionUrl = groupFrm.attr("action");

            if (selectedProposalRows.length < 2) {
                showAlert('danger', 'Group Creation Failed', 'You must select at least two proposals to create a group.');
                $("#groupModal").modal("hide");
                return;
            }


            // Create FormData object
            var formData = new FormData(groupFrm[0]);

            // Append selectedProposalRows properly
            selectedProposalRows.forEach((proposalId, index) => {
                formData.append(`proposals[${index}]`, proposalId);
            });

            $.ajax({
                method: "POST",
                url: actionUrl,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    showAlert(response.type, response.title, response.message);
                    if (response.type == 'success') {
                        selectedProposalRows = [];
                        $(".selectable-row").removeClass("selected-row");
                        location.reload(); // Refresh to reflect changes
                    }
                    console.log(response);
                },
                error: function (xhr) {
                    showAlert('danger', 'Error', (xhr.responseJSON ? xhr.responseJSON.error : "Unknown error"));
                }
            });

            $("#groupModal").modal("hide");
        });

        // Update Group
        $("#updateGroup").click(function (e) {
            e.preventDefault();
            let groupFrm = $("#groupFrm");
            var actionUrl = "{{ route('update_proposal_group', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}";

            // Create FormData object
            var formData = new FormData(groupFrm[0]);

            $.ajax({
                method: "POST",
                url: actionUrl,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.type == 'success') {
                        showAlert(response.type, response.title, response.message);
                        location.reload(); // Refresh to reflect changes
                    } else {
                        showAlert('danger', 'Error', response.message);
                    }
                    console.log(response);
                },
                error: function (xhr) {
                    showAlert('danger', 'Error', (xhr.responseJSON ? xhr.responseJSON.error : "Unknown error"));
                }
            });

            $("#groupModal").modal("hide");
        });

        checkPreviousMinutes();

        function checkPreviousMinutes() {
            let meetingId = $("input[name='meeting_id']").val();

            $.ajax({
                url: "{{ route('get.previous.minutes', ':meeting_id') }}".replace(':meeting_id', meetingId),
                type: "GET",
                success: function(response) {
                    if (response.success && response.previous_minutes) {
                        console.log("Previous Minutes Found:", response.previous_minutes);
                        $("#openMinutesModal").hide();
                        $("#viewButton").attr("href", "/storage/previous_minutes/" + response.previous_minutes).show();
                        $("#editMinutesButton").show();

                    } else {
                        console.log("No Previous Minutes Found");

                        // Show upload button & hide view button
                        $("#openMinutesModal").show();
                        $("#viewButton").hide();
                        $("#editMinutesButton").hide();

                    }
                }
            });
        }

        $("#editMinutesButton").click(function(e) {
            e.preventDefault();
            $("#editMinutesModal").modal("show");
        });

        $('#editMinutesForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route(getUserRole().'.upload.minutes') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        toastr.info("Uploading new file...", "", { timeOut: 0, extendedTimeOut: 0 });
                    },
                    success: function(response) {
                        toastr.clear();
                        if (response.success) {
                            toastr.success(response.message);

                            $('#editMinutesModal').modal('hide');
                            $("#editMinutesForm")[0].reset();
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(jqXHR) {

                        console.log(jqXHR);
                        // let errorMessage = "An error occurred. Please try again.";
                        // if (xhr.responseJSON && xhr.responseJSON.message) {
                        //     errorMessage = xhr.responseJSON.message;
                        // }
                        // toastr.error(errorMessage);
                    }
                });
            });
            // $('#editMinutesForm').submit();

        $("#openMinutesModal").click(function(e) {
            e.preventDefault();
            $("#previousMinModal").modal("show");
        });

        $('#uploadMinutesForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route(getUserRole().'.upload.minutes') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    toastr.info("Uploading previous minutes...", "", { timeOut: 0, extendedTimeOut: 0 }); // Show infinite loading
                },
                success: function(response) {
                    toastr.clear();
                    if (response.success) {
                        toastr.success(response.message);

                        $('#previousMinModal').modal('hide');
                        $("#uploadMinutesForm")[0].reset();
                        $("#openMinutesModal").remove();

                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    let errorMessage = "An error occurred. Please try again.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);

                }
            });
        });

    });

    function getImageByFileType(fileType) {
        switch (fileType) {
            case "pdf":
                return "{{ asset('assets/img/icons/file-icons/pdf.png') }}";
            case "xls":
                return "{{ asset('assets/img/icons/file-icons/xls.png') }}";
            case "xlsx":
                return "{{ asset('assets/img/icons/file-icons/xlsx.png') }}";
            case "csv":
                return "{{ asset('assets/img/icons/file-icons/csv-file.png') }}";
            default:
                return "{{ asset('assets/img/icons/file-icons/file.png') }}";
        }
    }
</script>
<script src="{{asset('assets/js/customFileUplaod.js')}}"></script>
<script src="{{asset('assets/js/orderOfBusiness.js')}}"></script>
<script src="{{asset('assets/js/proposal.js')}}"></script>

@endsection
