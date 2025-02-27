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
                <button type="button" class="nav-link active meeting-tab" role="tab" data-bs-toggle="tab" data-bs-target="#local-meetings" aria-controls="local-meetings" aria-selected="true" data-level = "0"><span class="d-flex align-items-center gap-2">
                    <i class='bx bx-book-content'></i>
                    <span class="d-none d-sm-block">Local Meetings</span> 
                </button>
            </li>
            <li class="nav-item mb-1 mb-sm-0">
                <button type="button" class="nav-link  meeting-tab" role="tab" data-bs-toggle="tab" data-bs-target="#university-meeting" aria-controls="university-meeting" aria-selected="false"  data-level = "1"><span class="d-flex align-items-center gap-2">
                    <i class='bx bx-book-content'></i>
                    <span class="d-none d-sm-block">University Meetings</span>
                </button>
            </li>            
            <li class="nav-item mb-1 mb-sm-0">
                <button type="button" class="nav-link meeting-tab" role="tab" data-bs-toggle="tab" data-bs-target="#board-meeting" aria-controls="board-meeting" aria-selected="false"  data-level = "2"><span class="d-flex align-items-center gap-2">
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
        <!-- <span class="text-muted">FILTER</span>
        <form method="POST" action="" class="d-flex gap-3" id="filterFrm">
            @csrf
            <div class="">
                <div class="d-flex gap-3 align-items-center">
                    
                    <div class="col-md-3 col-lg-auto" style="width: 120px">
                        <select name="year" class="form-select" id="yearSelect" aria-label="Select Year">
                            <option value="2025">2025</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-lg-auto">
                        <button type="submit" id="filterButton" style="min-width: 100px;" class="btn btn-success w-100 d-md-inline-flex align-items-center gap-2">
                            <i class='bx bx-filter-alt'></i>
                            <span>Filter</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <hr> -->
        <div class="card-datatable pt-0">
            <div class="table-responsive text-nowrap">
                <table id="meetingTable" class="datatables-basic table table-striped ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Level</th>
                            <th>Quarter</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Has OOB?</th>
                            <th>Council Type</th>
                            <th>Submission</th>
                            <th>Meeting Date</th>
                            <th>My Proposals</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="meetingsTableBody" class="table-border-bottom-0">
                        <tr>
                            <td  class="p-4">1</td>
                            <td>
                                {{ config('meetings.level.1') }}
                            </td>
                            <td>{{ config('meetings.quaterly_meetings.1') }}</td>
                            <td>2025</td>
                            <td>
                                <span class="badge bg-label-success me-1" style="text-transform: none;">
                                    {{ config('meetings.status.0') }}
                                </span>
                            </td>
                            <td>
                                
                                <span class="badge bg-label-success me-1" style="text-transform: none;">
                                    No
                                </span>
                            </td>
                            <td>
                                <div style="min-width: 200px">
                                    <small class="mb-0 align-items-center d-flex w-100 text-wrap gap-2">
                                        <i class='bx bx-radio-circle-marked text-primary'></i>
                                        {{ config('meetings.council_types.local_level.1') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    
                                </div>
                            </td>
                            <td>
                                
                            </td>
                                <td>
                                    <a href="" class="text-primary">
                                        <small>
                                            <i class='bx bx-file-blank' ></i>
                                            0 Proposals
                                        </small>
                                    </a>
                                </td>
                            <td>
                                <a class="btn btn-sm btn-danger d-flex gap-2 disabled">
                                    <i class='bx bx-lock'></i> Submission Closed
                                </a>
                            </td>
                        </tr>
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
