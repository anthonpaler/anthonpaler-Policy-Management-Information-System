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
    $actionColors = [ 'secondary', 'primary', 'success', 'warning', 'info', 'danger']; 
@endphp 
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex gap-3 justify-content-between align-items-center flex-wrap">
            <h5 class="m-0">
                {{ config('meetings.quaterly_meetings.'.$meeting->quarter) }} @if ($meeting->getMeetingCouncilType() == 0)
                    {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                @elseif ($meeting->getMeetingCouncilType() == 1)
                    {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                    @elseif ($meeting->getMeetingCouncilType() == 2)
                    {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
                @endif 
                {{ $meeting->year }}
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fw-light">Status: </span>
                <span class="badge bg-label-{{ $meeting->status == 0? 'success' : 'danger' }} me-1">
                    {{ config('meetings.status.'.$meeting->status) }}
                </span>
            </div>
        </div>
        <div class="d-flex justify-content-between w-100 mt-3">
            <div class="w-100 ">
                <div class="d-flex justify-content-between w-100 gap-3 flex-wrap">
                    <div class="">
                        
                    </div>
                    @if (session('isSecretary'))
                        <div class="d-flex justify-content-between gap-3">
                            <div class="d-flex gap-3 w-100 flex-wrap">
                                <div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary text-nowrap">Proposal Action</button>
                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>

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

                                                    if(auth()->user()->role == 5 ){
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
                                <div class="d-flex gap-2">
                                    <div class=" flex-grow-1">
                                        <input type="text" class="form-control flex-grow-1" data-id="" value="Select Action" id="proposalStatusInput"  disabled>
                                    </div>
                                    @if ($meeting->status == 1)
                                        <button type="button" class="btn btn-danger d-flex gap-2" disabled>
                                            <i class='bx bxs-lock-alt' ></i>
                                        </button>
                                    @else
                                        <button type="button" id="okActionButton" class="btn btn-success d-flex gap-2" {{ $meeting->status == 1 ? 'disabled': '' }}>
                                            <i class="fa-regular fa-circle-check"></i>
                                        </button>
                                    @endif
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
        <div class="d-flex justify-content-between">
            <div class="">
                <h5 class="mb-0">List of Meeting's Proposal</h5>
                <small class="text-muted">Proposals submitted for the scheduled meeting.</small>
            </div>
            <div class="d-flex gap-2">
                <div class="input-group input-group-merge">
                    <span  class="input-group-text">
                        <i class='bx bx-search' ></i>
                    </span>
                    <input type="text" class="form-control" id="proposalSearch" placeholder="Search...">
                </div>
                <div class="input-group input-group-merge">
                    <span  class="input-group-text">
                        <i class='bx bx-notepad'></i>
                    </span>
                    <select class="form-select" name="proposalStatus" required>
                        <option value="">All Status</option>
                        @foreach (config('proposals.status') as $key => $value)
                            <option value="{{ $value }}" >
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group input-group-merge">
                    <span  class="input-group-text">
                        <i class="bx bx-briefcase"></i></span>
                    </span>
                    <select class="form-select" name="proposalMatter" required>
                        <option value="">All Type of Matter</option>
                        @foreach (config('proposals.matters') as $key => $value)
                            <option value="{{ $value }}" >
                                {{ $value }}
                            </option>
                        @endforeach  
                    </select>
                </div>
                <div class="input-group input-group-merge">
                    <span  class="input-group-text">
                        <i class="bx bx-task"></i>
                    </span>
                    <select class="form-select" name="proposalAction" required>
                        <option value="">All Requested Action</option>
                        @foreach (config('proposals.requested_action') as $key => $value)
                            <option value="{{ $value }}" >
                                {{ $value }}
                            </option>
                        @endforeach                       
                    </select>
                </div>
            </div>
        </div>
        <div class="pt-4">
            <div class="table-responsive text-nowrap border-top">
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
                        @foreach($proposals as $proposal)
                        <tr data-proponent="{{ $proposal->proponent }}" data-title="{{ $proposal->title }}">
                            @if (session('isSecretary'))
                                @if ($meeting->status == 1)
                                    <td>
                                        <span class="text-danger"><i class='bx bxs-lock-alt' ></i></span>       
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
                                        @foreach ($proposal->proponentsList as $proponent)
                                            <div class="d-flex gap-3 align-items-center">
                                                <div data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $proponent->name }}" class="avatar avatar-sm pull-up">
                                                    <img class="rounded-circle" src="{{ $proponent->image ?? '/default-avatar.png' }}" alt="Avatar">
                                                </div>
                                                <span>{{ $proponent->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="min-width: 300px; max-width: 500px; white-space: wrap; ">
                                    <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }}" >{{ $proposal->proposal->title }}</a>
                                </div>
                            </td>
                            <td>
                                <span class="align-items-center d-flex gap-2"> 
                                    {!! $proposal->proposal->type == 1 ? "<i class='bx bx-book-content text-primary'></i> " : "<i class='bx bxs-book-content text-danger' ></i>" !!}

                                    {{ config('proposals.matters.'.$proposal->proposal->type) }}
                                </span>
                            </td>
                            <td> {{ config('proposals.requested_action.'.$proposal->proposal->action) }}</td>
                            <!-- <td>{{config('meetings.level.'.$proposal->proposal->getCurrentLevelAttribute())}}</td> -->
                            <td class="status-cell">
                                <div style="width: 230px; white-space: nowrap; ">
                                    <span class="mb-0 align-items-center d-flex w-px-100 gap-1">
                                        <i class='bx bx-radio-circle-marked text-{{ $actionColors[$proposal->status] ?? 'primary' }}'></i>
                                        {{ config('proposals.status.'.$proposal->status) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($proposal->files->count() > 0)
                                    <button class="btn btn-sm btn-success d-flex gap-2 view-files"
                                            data-files="{{ json_encode($proposal->files) }}" 
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
                                <div class="d-flex gap-2 align-items-center">
                                    @if(in_array(auth()->user()->role, [0,1,2,6]))
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }} ">
                                                    <i class="fa-regular fa-eye me-3"></i>View Details
                                                </a>
                                                
                                                @if(in_array($proposal->status, [2,5,6]))
                                                    <a class="dropdown-item" href="{{ route(getUserRole().'.proposal.edit', ['proposal_id' => encrypt($proposal->proposal->id)]) }}">
                                                        <i class='bx bx-right-arrow-circle me-3'></i>Resubmit Proposal
                                                    </a>
                                                @endif
                                                @if(!$proposal->is_edit_disabled)
                                                    <a class="dropdown-item" href="{{ route(getUserRole().'.proposal.edit', ['proposal_id' => encrypt($proposal->proposal->id)]) }}">
                                                        <i class="bx bx-edit-alt me-3"></i>Edit Proposal
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }} "  class="btn btn-sm btn-primary d-flex gap-2"><i class="fa-regular fa-eye" disabled></i>VIEW DETAILS</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title" id="fileModalLabel">File Preview</h5>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bx bx-fullscreen full-screen-file-preview" id="toggleIframeFullscreen"></i>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="fileIframe" src="" width="100%" height="600px" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    var proposalStatus = @json(config('proposals.status'));
</script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
<script src="{{asset('assets/js/dataTable.js')}}"></script>

@endsection
