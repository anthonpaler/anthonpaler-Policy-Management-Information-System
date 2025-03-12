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
            <div class="">
                <h5 class="mb-0">List of Meetings</h5>
                <small class="text-muted">Scheduled submissions and meetings with its proposals.</small>
            </div>
            <form method="POST" action="{{ route(getUserRole().'.meetings.filter') }}" class="d-flex gap-3" id="filterFrm">
                @csrf
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
                    <input type="text" name="level" id="level" class="form-control" value="{{session('user_role') == 3 ? 0 : (session('user_role') == 4 ? 1 : (session('user_role') == 5 ? 2 : 0))}}" hidden>  
                    <!-- <button class="btn btn-success d-flex gap-2" type="submit" id="filterButton" >
                        <i class='bx bx-filter-alt' ></i>
                        <span>Filter</span>
                    </button> -->
                </div>
            </form>
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
                            <th>Proposals</th>
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
                                    <td  class="p-4">{{ $loop->iteration }}</td>
                                    <td>
                                        {{ config('meetings.level.'.$meeting->getMeetingCouncilType()) }}
                                    </td>
                                    <td>{{ $meeting->getCampusName() }}</td></td>
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
                                                {{ config('meetings.council_types.local_level.1') }}
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
                                    <td>
                                        <a href="" class="text-primary">
                                            <span>
                                                <i class='bx bx-file-blank' ></i>
                                                {{ $meeting->proposals_count }} Proposals
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route(getUserRole().'.meetings.proposals', ['level' => $meeting->getMeetingLevel(), 'meeting_id' => encrypt($meeting->id)]) }}" class="btn btn-sm btn-primary d-flex gap-2"><i class="fa-regular fa-eye"></i>View Proposals</a>
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

