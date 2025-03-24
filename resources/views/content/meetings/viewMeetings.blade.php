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
@php 
    $actionColors = [ 'secondary', 'primary', 'success', 'warning', 'info', 'danger']; 
@endphp 
<div class="card mb-3">
    <div class="d-flex justify-content-between align-items-center custom_tab_wrapper">
        <div class="">
            <ul class="custom_tab_list" id="filterRow" data-action="{{ route(getUserRole().'.meetings.filter') }}">
                <li class="custom_tab_item meeting-tab {{ session('isProponent') || session('secretary_level') == 0 ? 'active' : '' }}" data-level = "0">
                    <div class="">
                        <i class='bx bx-book-open' ></i>
                        <span>Local Meetings</span>
                    </div>
                </li>
                <li class="custom_tab_item meeting-tab {{ session('secretary_level') == 1 ? 'active' : '' }} " data-level = "1">
                    <div class="">
                        <i class='bx bx-book-reader'></i>
                        <span>University Meetings</span>
                    </div>
                </li>
                <li class="custom_tab_item meeting-tab {{ session('secretary_level') == 2 ? 'active' : '' }}" data-level = "2">
                    <div class="">
                        <i class='bx bxs-book-reader' ></i>
                        <span>Board Meetings</span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="me-4">
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
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap">
            <div class="">
                <h5 class="mb-0">List of Meetings</h5>
                <small class="text-muted">Scheduled submissions and meetings.</small>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-merge">
                    <span  class="input-group-text">
                        <i class='bx bx-search' ></i>
                    </span>
                    <input type="text" class="form-control" id="meetingSearch" placeholder="Search...">
                </div>
                <div class="input-group input-group-merge">
                    <span  class="input-group-text">
                        <i class='bx bx-calendar-alt'></i>
                    </span>
                    <select class="form-select @error('year') is-invalid @enderror" name="year" required>
                        <option value="">All Year</option>
                        @foreach ($meetings->pluck('year')->unique()->sort() as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
 
        <div class="pt-4">
            <div class="table-responsive text-nowrap border-top">
                <table id="meetingTable" class="table table-striped">
                    <thead class="custom-tbl-header">
                        <tr>
                            <th>#</th>
                            <th>Level</th>
                            <th>Campus</th>
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
                            <td valign="top" colspan="11" class="dataTables_empty">
                                No data available in table
                            </td>
                        @else
                            @foreach($meetings as $index => $meeting)
                                <tr>
                                    <td  class="">{{ $loop->iteration }}</td>
                                    <td>
                                        {{ config('meetings.level.'.$meeting->getMeetingCouncilType()) }}
                                    </td>
                                    <td>{{ $meeting->getCampusName() }}</td>
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
                                            <span class="">Start: {{ \Carbon\Carbon::parse($meeting->submission_start)->format('F d, Y') }}</span>
                                            <span class="text-danger">End: {{ \Carbon\Carbon::parse($meeting->submission_end)->format('F d, Y') }}</span>
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
                                                        <i class='bx bx-lock'></i>CLOSED
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
<script src="{{asset('assets/js/dataTable.js')}}"></script>

<script>
    function showToastrWarning() {
        toastr.warning("Cannot generate an OOB because the meeting date is not yet set!");
    }
</script>
@endsection
