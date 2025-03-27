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
    <a href="#">Meeting's Proposal</a>
</div>
<div class="card">
@php 
    $actionColors = [ 'secondary', 'primary', 'success', 'warning', 'info', 'danger']; 
@endphp 
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap">
            <div class="mb-2">
                <h5 class="mb-0">List of Meetings</h5>
                <small class="text-muted">Scheduled submissions and meetings with its proposals.</small>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="flex-grow-1">
                    <div class="input-group input-group-merge">
                        <span  class="input-group-text">
                            <i class='bx bx-search' ></i>
                        </span>
                        <input type="text" class="form-control" id="meetingSearch" placeholder="Search...">
                    </div>
                </div>
                <div class="flex-grow-1">
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
        </div>
        <div class="pt-4">
            <div class="table-responsive text-nowrap">
                <table id="meetingTable" class="table table-striped">
                    <thead class="custom-tbl-header">
                        <tr>
                            <th>#</th>
                            <th style="max-width: 60px">Actions</th>
                            <th>Quarter</th>
                            <th>Year</th>
                            <th>Council Type</th>
                            <th>Campus</th>
                            <th>Submission</th>
                            <th>Meeting Date</th>
                            <th>Has OOB?</th>
                            <th>Status</th>
                            <th>Proposals</th>
                        </tr>
                    </thead>
                    <tbody id="meetingsTableBody" class="">
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
                                    <td  class="p-4">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2" style="max-width: 60px">
                                            <a class="action-btn primary"   href="{{ route(getUserRole().'.proposals.meetingProposals', ['level' => $meeting->getMeetingLevel(), 'meeting_id' => encrypt($meeting->id)]) }}">
                                                <i class="fa-regular fa-eye" style="font-size: .9em;"></i>
                                                <span class="tooltiptext">View Proposals</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }}</td>
                                    <td>{{ $meeting->year }}</td>
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
                                    <td>{{ $meeting->getCampusName() }}</td>
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
                                            <a href="{{ session('isProponent') ? route(getUserRole().'.meetings.myProposals', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) : '#' }}" class="text-primary">
                                                <span>
                                                    <i class='bx bx-file-blank' ></i>
                                                    {{ $meeting->proposals_count }} Proposals
                                                </span>
                                            </a>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-{{$meeting->has_order_of_business  ? 'primary' : 'danger'}}">
                                            {!! $meeting->has_order_of_business  ?  "<i class='bx bx-like' ></i> Yes"  : "<i class='bx bx-dislike' ></i> No" !!}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-{{$meeting->status == 0 ? 'primary' : 'danger'}}">
                                            {!! $meeting->status == 0 ? "<i class='bx bx-lock-open'></i>" : "<i class='bx bx-lock' ></i>" !!}
                                            {{ config('meetings.status.'.$meeting->status) }}
                                        </div>

                                    </td>
                                    <td>
                                        <a  href="{{ route(getUserRole().'.proposals.meetingProposals', ['level' => $meeting->getMeetingLevel(), 'meeting_id' => encrypt($meeting->id)]) }}" class="text-primary">
                                            <span>
                                                <i class='bx bx-file-blank' ></i>
                                                {{ $meeting->proposals_count }} Proposals
                                            </span>
                                        </a>
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

