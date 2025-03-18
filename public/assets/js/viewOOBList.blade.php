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
<div class="d-flex justify-content-between">
    <div class="nav-align-top mb-6">
        <ul class="nav nav-pills mb-4 nav-fill" role="tablist">
            <li class="nav-item mb-1 mb-sm-0">
                <button type="button" class="nav-link {{ in_array(auth()->user()->role, [0,1,2,3,6]) ? 'active':'' }} oob-tab" role="tab" data-bs-toggle="tab" data-bs-target="#local-meetings" aria-controls="local-meetings" aria-selected="true" data-level = "0"><span class="d-flex align-items-center gap-2">
                    <i class='bx bx-objects-horizontal-right'></i>
                    <span class="d-none d-sm-block">Local OOB</span> 
                </button>
            </li>
            <li class="nav-item mb-1 mb-sm-0">
                <button type="button" class="nav-link {{ auth()->user()->role == 4 ? 'active':'' }} oob-tab" role="tab" data-bs-toggle="tab" data-bs-target="#university-meeting" aria-controls="university-meeting" aria-selected="false"  data-level = "1"><span class="d-flex align-items-center gap-2">
                    <i class='bx bx-objects-horizontal-right'></i>
                    <span class="d-none d-sm-block">University OOB</span>
                </button>
            </li>            
            <li class="nav-item mb-1 mb-sm-0">
                <button type="button" class="nav-link {{ auth()->user()->role == 5   ? 'active':'' }} oob-tab" role="tab" data-bs-toggle="tab" data-bs-target="#board-meeting" aria-controls="board-meeting" aria-selected="false"  data-level = "2"><span class="d-flex align-items-center gap-2">
                    <i class='bx bx-objects-horizontal-right'></i>
                    <span class="d-none d-sm-block">Board OOB</span>
                </button>
            </li>
        </ul>
    </div>
    <div></div>
</div>
<!-- Basic Bootstrap Table -->
<div class="card">
    <h5 class="card-header">Order of Business List</h5>
    <div class="d-flex gap-3 ms-4">
        <form method="POST" action="{{ route(getUserRole().'.oob.filter') }}" class="d-flex gap-3" id="filterFrm">
        @csrf
            <!-- Year Filter -->
            <!-- <div class="col-md-3 col-lg-auto" style="width: 120px">
                <select name="year" class="form-select" id="yearSelect" aria-label="Select Year">
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                </select>
            </div> -->

            <input type="text" name="level" id="level" class="form-control" value="{{auth()->user()->role == 3 ? 0 : (auth()->user()->role == 2 ? 1 : 0)}}" hidden> 
            <!-- <div class="col-md-2 col-lg-auto">
                <button type="submit" id="filterButton" style="min-width: 100px;" class="btn btn-success w-100 d-md-inline-flex align-items-center gap-2">
                    <i class='bx bx-filter-alt'></i>
                    <span>Filter</span>
                </button>
            </div> -->
        </form>
    </div>
    <div class="card-body">
        <div class="card-datatable pt-0">
            <div class="table-responsive text-nowrap">
                <table class="datatables-basic table table-striped"  id="oobTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Level</th>
                            <th>Quarter</th>
                            <th>Year</th>
                            <th>Meeting Title</th>
                            <th>Status</th>
                            <th>Meeting Date & Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="" id="oobTableBody">
                        @if ($orderOfBusiness->isEmpty())
                            <!-- <tr>
                                <td colspan="8">
                                    <div class="alert alert-warning mt-3" role="alert">
                                        <i class="bx bx-info-circle"></i> No meetings found in the Order of Business.
                                    </div>
                                </td>
                            </tr> -->
                        @else
                            @foreach ($orderOfBusiness as $index => $oob)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ config('meetings.level.'.$oob->meetings->level) }}
                                    </td>
                                    <td>{{ config('meetings.quaterly_meetings.'.$oob->meetings->quarter) ?? 'N/A' }}</td>
                                    <td>{{ $oob->meetings->year }}</td>
                                    <td>
                                        {{ config('meetings.quaterly_meetings.'.$oob->meetings->quarter) }} 
                                            
                                                @if ($oob->meetings->level === 0)
                                                    {{ config("meetings.council_types.local_level.{$oob->meetings->council_type}") }}
                                                @endif
                                                @if ($oob->meetings->level === 1)
                                                    {{ config("meetings.council_types.university_level.{$oob->meetings->council_type}") }}
                                                @endif
                                                @if ($oob->meetings->level === 2)
                                                    {{ config("meetings.council_types.board_level.{$oob->meetings->council_type}") }}
                                                @endif
                                    
                                    </td>
                                    <td>
                                        <span class="badge 
                                            {{ $oob->status == 0 ? 'bg-label-warning' : 'bg-label-success' }} me-1">
                                            {{ $oob->status == 0 ? 'Draft' : 'Disseminated' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($oob->meetings->meeting_date_time)->format('l, M. d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route(getUserRole().'.order_of_business.view-oob', ['oob_id'=> encrypt( $oob->id)]) }}" 
                                        class="btn btn-sm btn-primary d-flex gap-2">
                                            <i class="fa-regular fa-eye"></i> VIEW DETAILS
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
    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileModalLabel">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="fileIframe" src="" width="100%" height="600px" frameborder="0"></iframe>
            </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/orderOfBusiness_v3.js') }}"></script>
<script src="{{asset('assets/js/pagination.js')}}"></script>
@endsection