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
<!-- Basic Bootstrap Table -->
<div class="card">
  <h5 class="card-header">List of Meetings</h5>
  <div class="card-body">
  <span class="text-muted">FILTER</span>
    <form method="POST" action="{{ route(getUserRole().'.proposals.filter') }}" class="d-flex gap-3" id="filterMeetingPropFrm">
        @csrf
        <div class="d-flex justify-content-between w-100">
            <div class="row g-3 align-items-center">
                <!-- Year Filter -->
                <div class="col-md-3 col-lg-auto" style="width: 120px">
                    <select name="year" class="form-select" id="yearSelect" aria-label="Select Year">
                        <option value="">All Year</option>
                        @foreach($meetings->pluck('year')->unique() as $year)
                            <option value="{{ $year }}" {{ request('year', now()->year) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Filter Button -->
                <div class="col-md-2 col-lg-auto">
                    <button type="submit" id="filterMeetingPropBtn" class="btn btn-success w-100 d-md-inline-flex align-items-center gap-2">
                        <i class='bx bx-filter-alt'></i>
                        <span>Filter</span>
                    </button>
                </div>
                <!-- <div class="col-md-2 col-lg-auto">
                    <a href="{{route(getUserRole().'.proposals')}}" class="btn btn-primary w-100 d-md-inline-flex align-items-center gap-2">
                        <i class='bx bx-refresh'></i>
                        <span>Refresh</span>
                    </a>
                </div> -->
            </div>
        </div>
    </form>
    <div class="mb-3 mt-4">
        <div class="table-responsive text-nowrap border border-top-0">
        <table class="table mb-5">
        <thead>
            <tr>
            <th>#</th>
            <th>Quarter</th>
            <th>Year</th>
            <th>Status</th>
            <th>Has OOB?</th>
            <th>Council Type</th>
            <th>Proposals</th>
            <th>Actions</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0" id="MeetingProposalTable">
            @if ($meetings->isEmpty())
                <tr>
                    <td colspan="10">
                        <div class="alert alert-warning mt-3" role="alert">
                            <i class="bx bx-info-circle"></i> There is no meetings at the moment.
                        </div>
                    </td>
                </tr>
            @endif
            @foreach($meetings as $meeting)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }}</td>
                <td>{{ $meeting->year }}</td>
                <td>
                    <span class="badge bg-label-{{$meeting->status == 0? 'success' : 'danger'}} me-1" style="text-transform: none;">
                        {{ config('meetings.status.'.$meeting->status) }}
                    </span>
                </td>
                <td>
                    @php
                        $hasOrderOfBusiness = \App\Models\OrderOfBusiness::where('meeting_id', $meeting->id)->exists() ? true : false
                    @endphp
                    <span class="badge bg-label-{{ $hasOrderOfBusiness ? 'success' : 'danger' }} me-1" style="text-transform: none;">
                        {{ $hasOrderOfBusiness ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-label-{{ config('settings.colors.council_types.'.$meeting->council_type) }} me-1" style="text-transform: none;">
                        @if ($meeting->level == 1)
                            {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                        @else
                            @if (isset($university) && $university == true)
                                {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                            @else 
                                {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                            @endif
                        @endif
                    </span>
                </td>
                <td><span class="badge bg-label-warning me-1" style="text-transform: none;">{{ $meeting->proposals_count }} Proposals</span></td>
                <td>
                <a href="{{ route(getUserRole().'.meetings.proposals', ['meeting_id' => encrypt($meeting->id)]) }}" class="btn btn-sm btn-primary d-flex gap-2"><i class="fa-regular fa-eye"></i>View Proposals</a>
            </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
  </div>
</div>
<script>
    var filterMeetingPropFrm = $("#filterMeetingPropFrm");
    var filterMeetingPropBtn = $("#filterMeetingPropBtn");
    filterMeetingPropBtn.on('click', function(event){
        event.preventDefault();
        // showAlert("info", "FYI!", "This Feature is still in progress");

        var actionUrl = filterMeetingPropFrm.attr('action');
        $.ajax({
            method: "POST",
            url: actionUrl,
            data: filterMeetingPropFrm.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                filterMeetingPropBtn.html(`<i class='bx bx-loader-alt bx-spin' ></i>
                    <span>Filtering...</span> `).prop('disabled', true);
            },
            success: function (response) {
                filterMeetingPropBtn.html(`<i class='bx bx-filter-alt'></i>
                    <span>Filter</span> `).prop('disabled', false);
                if(response.type == 'success'){
                    $('#MeetingProposalTable').html(response.html);
                    showAlert("success", "Filtered", "Meeting filtered successfully!");
                }else{
                    showAlert("danger", "Can't Filter", "Something went wrong!");
                }
            },            
            error: function (xhr, status, error) {
                filterMeetingPropBtn.html(`<i class='bx bx-filter-alt'></i>
                    <span>Filter</span> `).prop('disabled', false);
                console.log(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert("danger", response.title, response.message);
            }
        });
    });
</script>
<!-- <script src="{{asset('assets/js/proposal.js')}}"></script> -->
@endsection
