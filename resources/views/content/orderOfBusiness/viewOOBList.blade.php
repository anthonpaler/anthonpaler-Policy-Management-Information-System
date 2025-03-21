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
    <a href="#">Order of Business</a>
</div>
<div class="card mb-3">
    <div class="d-flex justify-content-between align-items-center custom_tab_wrapper">
        <div class="">
            <ul class="custom_tab_list" id="filterRow" data-action="{{ route(getUserRole().'.oob.filter') }}">
                <li class="custom_tab_item oob-tab {{ session('isProponent') || session('secretary_level') == 0 ? 'active' : '' }}" data-level="0">
                    <div class="">
                        <i class='bx bx-book-open'></i>
                        <span>Local OOB</span>
                    </div>
                </li>
                <li class="custom_tab_item oob-tab {{ session('secretary_level') == 1 ? 'active' : '' }}" data-level="1">
                    <div class="">
                        <i class='bx bx-book-reader'></i>
                        <span>University OOB</span>
                    </div>
                </li>
                <li class="custom_tab_item oob-tab {{ session('secretary_level') == 2 ? 'active' : '' }}" data-level="2">
                    <div class="">
                        <i class='bx bxs-book-reader'></i>
                        <span>Board OOB</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Basic Bootstrap Table -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap">
            <div class="mb-3">
                <h5 class="mb-0">Order of Business Overview</h5>
                <small class="text-muted">List of meetings in the Order of Business.</small>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-merge">
                    <span  class="input-group-text">
                        <i class='bx bx-search' ></i>
                    </span>
                    <input type="text" class="form-control" id="oobSearch" placeholder="Search...">
                </div>
                <div class="input-group input-group-merge">
                    <span  class="input-group-text">
                        <i class='bx bx-calendar-alt'></i>
                    </span>
                    <select class="form-select @error('year') is-invalid @enderror" name="year" required>
                        <option value="">All Year</option>
                        @foreach ($orderOfBusiness->pluck('meeting.year')->unique()->sort() as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-datatable pt-0">
            <div class="table-responsive text-nowrap">
                <table class="datatables-basic table table table-striped" id="oobTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Level</th>
                            <th>Campus</th>
                            <th>Quarter</th>
                            <th>Year</th>
                            <th>Meeting Title</th>
                            <th>Status</th>
                            <th>Meeting Date & Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="oobTableBody">
                    @forelse ($orderOfBusiness as $index => $oob)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ config('meetings.level.'.$oob->meeting->getMeetingCouncilType()) }}
                            </td>
                            <td>{{ $oob->meeting->getCampusName() }}</td>
                            <td>{{ config('meetings.quaterly_meetings.'.$oob->meeting->quarter) ?? 'N/A' }}</td>
                            <td>{{ $oob->meeting->year }}</td>
                            <td>
                                {{ config('meetings.quaterly_meetings.'.$oob->meeting->quarter) }} 
                                    
                                    @if ($oob->meeting->getMeetingCouncilType() === 0)
                                        {{ config("meetings.council_types.local_level.{$oob->meeting->council_type}") }}
                                    @endif
                                    @if ($oob->meeting->getMeetingCouncilType() === 1)
                                        {{ config("meetings.council_types.university_level.{$oob->meeting->council_type}") }}
                                    @endif
                                    @if ($oob->meeting->getMeetingCouncilType() === 2)
                                        {{ config("meetings.council_types.board_level.{$oob->meeting->council_type}") }}
                                    @endif
                            </td>
                            <td>
                                <span class="badge 
                                    {{ $oob->status == 0 ? 'bg-label-warning' : 'bg-label-success' }} me-1">
                                    {{ $oob->status == 0 ? 'Draft' : 'Disseminated' }}
                                </span>
                            </td>
                            <td>
                                <span>
                                    {{ $oob->meeting->meeting_date_time ? \Carbon\Carbon::parse($oob->meeting->meeting_date_time)->format('F d, Y, h:i A') : 'Not yet set' }}
                                </span>  
                            </td>
                            <td>
                                <a href="{{ route(getUserRole().'.order_of_business.view-oob', ['level' => $oob->meeting->getMeetingLevel(), 'oob_id'=> encrypt( $oob->id)]) }}" 
                                class="btn btn-sm btn-primary d-flex gap-2">
                                    <i class="fa-regular fa-eye"></i> VIEW OOB
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-warning mt-3" role="alert">
                                    <i class="bx bx-info-circle"></i> No meetings found in the Order of Business.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/orderOfBusiness.js') }}"></script>
<script src="{{asset('assets/js/dataTable.js')}}"></script>

@endsection