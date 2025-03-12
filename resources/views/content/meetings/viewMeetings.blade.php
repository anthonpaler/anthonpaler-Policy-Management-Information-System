@extends('layouts/contentNavbarLayout')

@section('title', 'Meetings')

@section('content')
<div class="bread-crumbs overflow-auto" style="max-width: 100%; white-space: nowrap;">
    <h5>Dashboard</h5>
    <div class="divider"></div>
    <a href="/">
        <i class='bx bx-home-alt' ></i>
    </a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#">Meetings</a>
</div>
<div class="d-flex justify-content-between">
    <div class="nav-align-top mb-6">
        <ul class="nav nav-pills mb-4 nav-fill" role="tablist">
            <li class="nav-item mb-1 mb-sm-0">
                <button type="button" class="nav-link {{ session('isProponent') || session('secretary_level') == 0 ? 'active' : '' }} meeting-tab" role="tab" data-bs-toggle="tab" data-bs-target="#local-meetings" aria-controls="local-meetings" aria-selected="true" data-level = "0"><span class="d-flex align-items-center gap-2">
                    <i class='bx bx-book-content'></i>
                    <span class="d-none d-sm-block">Local Meetings</span> 
                </button>
            </li>
            <li class="nav-item mb-1 mb-sm-0">
                <button type="button" class="nav-link  {{ session('secretary_level') == 1 ? 'active' : '' }}  meeting-tab" role="tab" data-bs-toggle="tab" data-bs-target="#university-meeting" aria-controls="university-meeting" aria-selected="false"  data-level = "1"><span class="d-flex align-items-center gap-2">
                    <i class='bx bx-book-content'></i>
                    <span class="d-none d-sm-block">University Meetings</span>
                </button>
            </li>            
            <li class="nav-item mb-1 mb-sm-0">
                <button type="button" class="nav-link meeting-tab  {{ session('secretary_level') == 2 ? 'active' : '' }} " role="tab" data-bs-toggle="tab" data-bs-target="#board-meeting" aria-controls="board-meeting" aria-selected="false"  data-level = "2"><span class="d-flex align-items-center gap-2">
                    <i class='bx bx-book-content'></i>
                    <span class="d-none d-sm-block">Board Meetings</span>
                </button>
            </li>
        </ul>
    </div>
    <div class="">
        @if(in_array(session('user_role'), [3,4,5]))
            <div>
                <a href="{{ route(getUserRole().'.view_create_meeting') }}" class="btn btn-primary d-flex gap-2">
                    <i class='bx bxs-megaphone'></i>
                    <span class="text-nowrap"> Call for Submission</span>
                </a>
            </div>
        @endif
    </div>
</div>
<div class="card">
@php 
    $actionColors = [ 'secondary', 'primary', 'success', 'warning', 'info', 'danger']; 
@endphp 
    <div class="card-body">
        <h5>List of Meetings</h5>
        <!-- <span class="text-muted">FILTER</span> -->
        <form method="POST" action="{{ route(getUserRole().'.meetings.filter') }}" class="d-flex gap-3" id="filterFrm">
            @csrf
            <div class="">
                <div class="d-flex gap-3 align-items-center">
                    <input type="text" name="level" id="level" class="form-control" value="{{session('user_role') == 3 ? 0 : (session('user_role') == 4 ? 1 : (session('user_role') == 5 ? 2 : 0))}}" hidden>  
                    <!-- <div class="col-md-2 col-lg-auto">
                        <button type="submit" id="filterButton" style="min-width: 100px;" class="btn btn-success w-100 d-md-inline-flex align-items-center gap-2">
                            <i class='bx bx-filter-alt'></i>
                            <span>Filter</span>
                        </button>
                    </div> -->
                </div>
            </div>
        </form>
        <div class="card-datatable pt-0">
            <div class="table-responsive text-nowrap">
                <table id="meetingTable" class="datatables-basic table table-striped ">
                    <thead>
                        <tr>
                            <th>#</th>
                            @if(session('isSecretary'))
                                <th>Level</th>
                            @endif
                            <!-- <th>Level</th> -->
                            <th>Quarter</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Has OOB?</th>
                            <th>Council Type</th>
                            <th>Submission</th>
                            <th>Meeting Date</th>
                            @if(session('isProponent'))
                                <th>My Proposals</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="meetingsTableBody" class="table-border-bottom-0">
                        @if ($meetings->isEmpty())
                            <tr>
                                <td colspan="10">
                                    <div class="alert alert-warning mt-3" role="alert">
                                        <i class="bx bx-info-circle"></i> There is no meetings at the moment.
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($meetings as $index => $meeting)
                                <tr>
                                    <td  class="">{{ $loop->iteration }}</td>
                                    @if(session('isSecretary'))
                                        <td>
                                            {{ config('meetings.level.0') }}
                                        </td>
                                    @endif
                                    <td>{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }}</td>
                                    <td>{{ $meeting->year }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-{{$meeting->status == 0 ? 'primary' : 'danger'}}">
                                            {!! $meeting->status == 0 ? "<i class='bx bxs-lock-open-alt' ></i>" : "<i class='bx bxs-lock-alt' ></i>" !!}
                                            {{ config('meetings.status.'.$meeting->status) }}
                                        </div>

                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-{{$meeting->has_order_of_business  ? 'primary' : 'danger'}}">
                                            {!! $meeting->has_order_of_business  ?  "<i class='bx bxs-like' ></i> Yes"  : "<i class='bx bxs-dislike' ></i> No" !!}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="min-width: 200px">
                                            <span class="mb-0 align-items-center d-flex w-100 text-wrap gap-2">
                                                <i class='bx bx-radio-circle-marked text-{{ $actionColors[$meeting->council_type] ?? 'primary' }}'></i>
                                                    @if ($meeting->getMeetingCouncilType() == 0)
                                                        {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                                                    @elseif ($meeting->getMeetingCouncilType() == 1)
                                                        {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                                                    @elseif ($meeting->getMeetingCouncilType() == 2)
                                                        {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
                                                    @endif 
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class=""><span class="text-primary">Start: </span>{{ \Carbon\Carbon::parse($meeting->submission_start)->format('F d, Y') }}</span>
                                            <span class=""><span class="text-danger">End: </span> {{ \Carbon\Carbon::parse($meeting->submission_end)->format('F d, Y') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>
                                            {{ $meeting->meeting_date_time ? \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, h:i A') : 'Not yet set' }}
                                        </span>  
                                    </td>
                                    @if(session('isProponent'))
                                        <td>
                                            <a href="" class="text-primary">
                                                <span>
                                                    <i class='bx bx-file-blank' ></i>
                                                    {{ $meeting->proposals_count }} Proposals
                                                </span>
                                            </a>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(session('isProponent'))
                                                @if ($meeting->getIsSubmissionClosedAttribute() || $meeting->status == 1)
                                                    <a class="btn btn-sm btn-danger d-flex gap-2 disabled">
                                                        <i class='bx bx-lock'></i>Closed
                                                    </a>
                                                @else
                                                    <a class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                                        id="submitProposal"
                                                        data-meetingStatus="{{ $meeting->status }}"
                                                        href="{{ route(getUserRole().'.meetings.submit-proposal', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}"
                                                    >
                                                        <i class='bx bx-send'></i> SUBMIT
                                                    </a>
                                                @endif
                        
                                                <a class="btn btn-sm btn-success d-flex align-items-center gap-1"
                                                    data-meetingStatus="{{ $meeting->status }}"
                                                   href="{{ route(getUserRole().'.meetings.details', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}"
                                                >
                                                    <i class='bx bx-right-top-arrow-circle'></i>VIEW
                                                </a>
                                            @endif
                                           
                                            @if(session('isSecretary'))

                                                <a class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                                   href="{{ route(getUserRole().'.meetings.details', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}" 
                                                >
                                                    <i class='bx bx-right-top-arrow-circle'></i>VIEW
                                                </a>

                                                <a class="btn btn-sm btn-success d-flex align-items-center gap-1"
                                                   href="{{ route(getUserRole().'.meeting.edit_meeting', ['level' => $meeting->getMeetingLevel(), 'meeting_id' => Crypt::encrypt($meeting->id)])}}"
                                                >
                                                    <i class='bx bx-edit'></i> EDIT
                                                </a>

                                                @if ($meeting->status == 1)
                                                    <a class="btn btn-sm btn-danger d-flex gap-2 disabled">
                                                        <i class='bx bx-lock'></i> CLOSED
                                                    </a>
                                                @else
                                                    <div class="dropdown">
                                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>
                                                        <div class="dropdown-menu">

                                                        @if(!$meeting->has_order_of_business)
                                                            @if($meeting->meeting_date_time) 
                                                                <a class="dropdown-item" href="{{ route(getUserRole().'.order_of_business.view-generate', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}">
                                                                    <i class='bx bx-up-arrow-circle me-1'></i> Generate OOB
                                                                </a>
                                                            @else
                                                                <a class="dropdown-item text-danger" href="#" onclick="showToastrWarning()">
                                                                    <i class='bx bx-up-arrow-circle me-1'></i> Generate OOB
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
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
<script src="{{asset('assets/js/meetings.js')}}"></script>
<script src="{{asset('assets/js/pagination.js')}}"></script>

<script>
    function showToastrWarning() {
        toastr.warning("Cannot generate an OOB because the meeting date is not yet set!");
    }
</script>
@endsection
