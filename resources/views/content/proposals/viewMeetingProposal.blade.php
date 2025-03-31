@extends('layouts/contentNavbarLayout')

@section('title', 'Proposals')

@section('content')
<div class="d-flex justify-content-between">
    <div class="bread-crumbs overflow-auto" style="max-width: 100%; white-space: nowrap;">
        <h5>Dashboard</h5>
        <div class="divider"></div>
        <a href="/">
            <i class='bx bx-home-alt' ></i>
        </a>
        <i class='bx bx-chevron-right' ></i>
        <a href="{{route(    getUserRole().'.proposals')}}">Meetings Proposals</a>
        <i class='bx bx-chevron-right' ></i>
        <a href="#">Proposals</a>
    </div>

</div>
@php 
    $actionColors = ['secondary', 'primary', 'success', 'warning', 'info', 'danger']; 
    $statusCounts = [
        'For Endorsement' => 0,
        'Posted to Agenda' => 0,
        'Approved' => 0,
        'Endorsed' => 0,
        'Returned' => 0,
        'Deferred' => 0,
    ];

    foreach ($proposals as $proposal) {
        $status = config('proposals.status.' . $proposal->status);
        if (isset($statusCounts[$status])) {
            $statusCounts[$status]++;
        }
    }
@endphp 
<div class="row">
<!-- /single card  -->
  <div class="col-lg-2 col-sm-6 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex gap-2 justify-content-between">
          <div class="card-info">
            <p class="text-heading mb-1">For Endorsment</p>
            <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">{{ $statusCounts['For Endorsement'] }}</h4>
                <span class="text-primary"> proposals</span>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class='bx bx-trending-up'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex gap-2 justify-content-between">
          <div class="card-info">
            <p class="text-heading mb-1">Posted to Agenda</p>
            <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">{{ $statusCounts['Posted to Agenda'] }}</h4>
              <span class="text-info">proposals</span>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
                <i class='bx bx-book-content' ></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex gap-2 justify-content-between">
          <div class="card-info">
            <p class="text-heading mb-1">Approved</p>
            <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">{{ $statusCounts['Approved'] }}</h4>
              <span class="text-success">proposals</span>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
                <i class='bx bx-like' ></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex gap-2 justify-content-between">
          <div class="card-info">
            <p class="text-heading mb-1">Endorsed</p>
            <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">{{ $statusCounts['Endorsed'] }}</h4>
                <span class="text-info">proposals</span>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
                <i class='bx bx-up-arrow-circle' ></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex gap-2 justify-content-between">
          <div class="card-info">
            <p class="text-heading mb-1">Returned</p>
            <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">{{ $statusCounts['Returned'] }}</h4>
                <span class="text-warning">proposals</span>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
                <i class='bx bx-left-arrow-circle'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex gap-2 justify-content-between">
          <div class="card-info">
            <p class="text-heading mb-1">Deffered</p>
            <div class="d-flex align-items-center mb-1">
                <h4 class="card-title mb-0 me-2">{{ $statusCounts['Deferred'] }}</h4>
                <span class="text-danger">proposals</span>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-danger rounded p-2">
                <i class='bx bx-dislike' ></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between w-100">
            <div class="w-100 ">
                <div class="d-flex justify-content-between w-100 gap-3 flex-wrap align-items-center">
                    <div class="">
                        <h5 class="m-0">
                            {{ config('meetings.quaterly_meetings.'.$meeting->quarter) }} @if ($meeting->getMeetingCouncilType() == 0)
                                {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                            @elseif ($meeting->getMeetingCouncilType() == 1)
                                {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                                @elseif ($meeting->getMeetingCouncilType() == 2)
                                {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
                            @endif 
                            {{ $meeting->year }}
                            <!-- <span class="ms-2 badge bg-label-{{ $meeting->status == 0? 'success' : 'danger' }} me-1">
                                {{ config('meetings.status.'.$meeting->status) }}
                            </span> -->
                        </h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
                            <small>Submission Period : </small>
                            <small class="">{{ \Carbon\Carbon::parse($meeting->submission_start)->format('F d, Y') }}</small>
                            <small> - </small>
                            <small class="text-danger">{{ \Carbon\Carbon::parse($meeting->submission_end)->format('F d, Y') }}</small>
                        </div>
                    </div>
                    @if (session('isSecretary'))
                        <div class="d-flex justify-content-between gap-3">
                            <div class="d-flex gap-3 w-100 flex-wrap">
                                <div>
                                    <button type="button" class="btn btn-success text-nowrap d-flex gap-2 align-items-center" data-bs-toggle="modal" data-bs-target="#proposalModal">
                                        <i class='bx bx-book-add mt-1' style="font-size: .97em;"></i> Add Proposal
                                    </button>
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <div>
                                        <div class="btn-group">
                                            <button type="button" class="form-control dropdown-toggle d-flex gap-2 justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false" data-id="" id="proposalStatusInput" >Proposal Actions</button>

                                            @php
                                                $currentDateTime = now();
                                                $meetingDateTime = \Carbon\Carbon::parse($meeting->meeting_date_time);
                                            @endphp

                                            <ul class="dropdown-menu">
                                                @foreach (array_slice(config('proposals.proposal_action'), 0, 7, true) as $index => $item)
                                                    @php
                                                        // Skip indices 2, 4, 5, and 6
                                                        if (in_array($index, [1, 4, 5, 6])) {
                                                            continue;
                                                        }

                                                        if(session('user_role') == 5 ){
                                                            if (in_array($index, [3])) {
                                                                continue;
                                                            }
                                                        }

                                                        $isDisabled = true; 
                                                        
                                                        if ($meetingDateTime) {
                                                            if ($currentDateTime->greaterThan($meetingDateTime)) {
                                                                $isDisabled = in_array($index, [0, 1]); // Enable 0 & 1 if current date is before meeting date
                                                            }
                                                            else {
                                                                $isDisabled = in_array($index, [2, 3, 4, 5, 6]); // Enable 2-6 if current date is after or equal to meeting date
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    <li>
                                                        <span class="dropdown-item proposal-action {{ $index === 6 ? 'text-danger' : '' }} {{ $isDisabled ? 'disabled' : '' }}" 
                                                            data-id="{{ $index }}" 
                                                            data-label="{{ $item }}">
                                                            {{ $item }}
                                                        </span>
                                                    </li>

                                                    @if (in_array($index, [1, 5])) 
                                                        <li><hr class="dropdown-divider"></li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- <div class=" flex-grow-1">
                                        <input type="text" class="form-control flex-grow-1" data-id="" value="Select Action" id="proposalStatusInput"  disabled>
                                    </div> -->
                                    <div>
                                        <button class="btn btn-{{ $meeting->status == 1 ? 'danger': 'primary' }} d-flex gap-2 align-items-center text-nowrap" 
                                                id="updateMultiProposalBtn" 
                                                data-id="{{ encrypt($proposal->id) }}" 
                                                {{ $proposal->meeting->status == 1 ? 'disabled' : '' }}>
                                            
                                            {!! $proposal->meeting->status == 1 ? "<i class='bx bx-lock-alt'></i>" : "<i class='bx bx-send'></i>" !!}
                                            Update Proposal Status
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Basic Bootstrap Table -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap">
            <div class="mb-2">
                <h5 class="mb-0">List of Meeting's Proposal</h5>
                <small class="text-muted">Proposals submitted for the scheduled meeting.</small>
            </div>
            <div class="d-flex gap-2">
                <div>
                    <div class="input-group input-group-merge">
                        <input type="text" class="form-control" id="proposalSearch" placeholder="Search...">    
                        <span  class="input-group-text">
                            <i class='bx bx-search' ></i>
                        </span>
                    </div>
                </div>

                <div class="btn-group">
                    <button class="btn btn-primary btn-icon dropdown-toggle  hide-arrow" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class='bx bx-filter-alt'></i></button>
                    <ul class="dropdown-menu p-4" style="width: 300px;">
                        <h5 class="m-0 text-primary">Filter Proposal</h5>
                        <div class="mb-3 mt-3">
                            <small class="">STATUS</small>
                            <select class="form-select" name="proposalStatus" required>
                                <option value="">All Status</option>
                                @foreach (config('proposals.status') as $key => $value)
                                    <option value="{{ $value }}" >
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <small class="f">TYPE OF MATTER</small>
                            <select class="form-select" name="proposalMatter" required>
                                <option value="">All Type of Matter</option>
                                @foreach (config('proposals.matters') as $key => $value)
                                    <option value="{{ $value }}" >
                                        {{ $value }}
                                    </option>
                                @endforeach  
                            </select>
                        </div>

                        <div class="mb-3">
                            <small class="">REQUESTED ACTION</small>
                            <select class="form-select" name="proposalAction" required>
                                <option value="">All Requested Action</option>
                                @foreach (config('proposals.requested_action') as $key => $value)
                                    <option value="{{ $value }}" >
                                        {{ $value }}
                                    </option>
                                @endforeach                       
                            </select>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
        <div class="pt-4">
            <div class="table-responsive text-nowrap">
                <table id="proposalTable" class="table table-striped w-100">
                    <thead class="custom-tbl-header">
                        <tr>
                            @if (session('isSecretary'))
                                <td>
                                    <!-- <input type="checkbox" class="form-check-input"> -->
                                </td>
                            @endif
                            <th>#</th>
                            <th>Proponent</th>
                            <th>Proposal Title</th>
                            <th>Type</th>
                            <th>Requested Action</th>
                            <!-- <th>Current Level</th> -->
                            <th>Status</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @if ($proposals->isEmpty())
                        
                        @else

                            @foreach($proposals as $proposal)
                            <tr data-proponent="{{ $proposal->proponent }}" data-title="{{ $proposal->title }}">
                                @php
                                    // Define proposal status classes dynamically
                                    $statusClass = match ($proposal->status) {
                                        2, 7 => 'danger',
                                        5, 6, 8 => 'warning',
                                        1, 9 => 'primary',
                                        3, 10 => 'success',
                                        4 => 'info',
                                        default => 'secondary'
                                    };
                                @endphp

                                @if (session('isSecretary'))
                                    @if ($meeting->status == 1)
                                        <td>
                                            <span class="text-danger"><i class='bx bx-lock-alt' ></i></span>       
                                        </td>                 
                                    @else
                                        @if($meetingDateTime && $currentDateTime->lessThan($meetingDateTime))
                                            <td>
                                                <input type="checkbox" 
                                                class="form-check-input select-proposal" 
                                                data-id="{{ encrypt($proposal->proposal->id) }}" 
                                                {{ (!in_array($proposal->status , [0, 8])) ? 'disabled' : '' }}>
                                            </td>
                                        @elseif($meetingDateTime && $currentDateTime->greaterThan($meetingDateTime))
                                            <td>
                                                <input type="checkbox" 
                                                class="form-check-input select-proposal" 
                                                data-id="{{ encrypt($proposal->proposal->id) }}" 
                                                {{ (!in_array($proposal->status , [1])) ? 'disabled' : '' }}>
                                            </td>
                                        @endif
                                    @endif                            
                                @endif
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-column gap-3">
                                            @foreach ($proposal->proposal->proponents as $proponent)
                                                <div class="d-flex align-items-center gap-3">
                                                    <img class="rounded-circle avatar-sm" src="{{ $proponent->image && trim($proponent->image) !== '' ? $proponent->image : asset('assets/img/avatars/default-avatar.jpg') }}
    " alt="Avatar">
                                                    <span>{{ $proponent->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="white-space: wrap;">
                                        <a style="color: #697A8D;" href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }}" >{{ $proposal->proposal->title }}</a>
                                    </div>
                                </td>
                                <td>
                                    <span class="align-items-center d-flex gap-2"> 
                                        {!! $proposal->proposal->type == 1 ? "<i class='bx bx-book-content text-primary'></i> " : "<i class='bx bxs-book-content text-danger' ></i>" !!}

                                        {{ config('proposals.matters.'.$proposal->proposal->type) }}
                                    </span>
                                </td>
                                <td> 
                                    <span class="d-flex gap-2 align-items-center">
                                        <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal->proposal->action] ?? 'primary' }}'></i>
                                        {{ config('proposals.requested_action.'.$proposal->proposal->action) }}
                                    </span>
                                </td>
                                <!-- <td>{{config('meetings.level.'.$proposal->proposal->getCurrentLevelAttribute())}}</td> -->
                                <td class="status-cell">
                                    <div style="width: 230px; white-space: nowrap; ">
                                        <span class="mb-0 align-items-center d-flex w-px-100 gap-1">
                                            <i class='bx bx-radio-circle-marked text-{{$statusClass}}'></i>
                                            {{ config('proposals.status.'.$proposal->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if($proposal->proposal->files->count() > 0)
                                        <!-- <button class="btn btn-sm btn-secondary d-flex gap-2 view-files"
                                                data-files="{{ json_encode($proposal->proposal->files) }}" 
                                                data-title="{{ $proposal->proposal->title }}">
                                            <i class='bx bx-file'></i> {{ $proposal->proposal->files->where('is_active', 1)->count() }}
                                            FILES
                                        </button> -->
                                        <button class="btn btn-sm btn-secondary d-flex gap-2 view-files"
                                                data-files="{{ json_encode($proposal->proposal->files) }}" 
                                                data-title="{{ $proposal->proposal->title }}">
                                            <i class='bx bx-file'></i> VIEW FILES
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-danger d-flex gap-2">
                                            <i class='bx bx-file'></i> NO FILES
                                        </button>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }} "  class="btn btn-sm btn-primary d-flex gap-2" style="max-width: 142px;"><i class="fa-regular fa-eye" disabled></i>VIEW PROPOSAL</a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal Files-->
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


<!-- ADD PROOPOSAL MODAL -->
<div class="modal fade" id="proposalModal" tabindex="-1" aria-labelledby="proposalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="proposalModalLabel">Proposal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route(getUserRole().'.addProposal', ['meeting_id' => encrypt($meeting->id)]) }}" enctype="multipart/form-data" id="proposalFrm">
                    @csrf
              
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label" for="title">Title <span class="ms-1 text-danger">*</span></label>
                        <textarea id="title" name="title" class="form-control" placeholder="Enter title" required rows="3"></textarea>
                        @error('title')
                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Proponent or Presenter Email -->
                    <div class="mb-3">
                        <label class="form-label" for="proponent_email">Proponent<span class="ms-1 text-danger">*</span></label>
                        <div class="input-group">
                            <span id="email-icon" class="input-group-text"><i class="bx bx-envelope"></i></span>
                            <input 
                                type="text" 
                                id="proponent_email" 
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
                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Add Proposal</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    @if(session('toastr'))
        toastr["{{ session('toastr.type') }}"]("{{ session('toastr.message') }}");
    @endif
</script>


<script>
    var proposalStatus = @json(config('proposals.status'));

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
<script>
document.addEventListener("DOMContentLoaded", function () {
        let emailInput = document.getElementById("proponent_email");
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

            document.querySelector("#proposalFrm").addEventListener("submit", function (e) {
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
                    url: "{{route(getUserRole().'.fetchProponents')}}",
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
</script>
<script src="{{asset('assets/js/customFileUplaod.js')}}"></script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
<script src="{{asset('assets/js/dataTable.js')}}"></script>

@endsection
