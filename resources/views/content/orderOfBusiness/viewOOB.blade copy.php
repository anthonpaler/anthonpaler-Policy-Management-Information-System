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
    <a href="{{ route(getUserRole().'.order-of-business')}}" >Order of Business</a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#" >Order of Business Information</a>
</div>
<div class="card p-4">

    <div class="mt-3">
        <button type="button" id="exportOOB" class="btn btn-secondary d-flex gap-2 {{$orderOfBusiness->status == 0 ? 'd-none' : ''}}">
            <a href="{{ route('oob.export.pdf', ['level' => $orderOfBusiness->meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}" class="text-white d-flex align-items-center gap-2" target="_blank"><i class='bx bx-export'></i> Export OOB</a>
        </button>
    </div>
    
    <form action="{{ route(getUserRole().'.order_of_business.save', ['level' => $meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}" method="post" id="oobFrm" meeting-id="{{encrypt($orderOfBusiness->meeting->id)}}">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <h4 class="card-header p-0 mb-2 text-center">
                {{ config('meetings.quaterly_meetings.'.$meeting->quarter) }} 
                @if ($meeting->getMeetingCouncilType() == 0)
                    {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                @elseif ($meeting->getMeetingCouncilType() == 1)
                    {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                @elseif ($meeting->getMeetingCouncilType() == 2)
                    {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
                @endif 
                {{ $meeting->year }}
            </h4> 
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center ">
                <span class="text-muted fw-light text-center">{{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, l, h:i A') }}</span>

                @if ($meeting->modality == 1 || $meeting->modality == 3)
                <span> | Venue  at  {{$meeting->venue->name}}</span>
                @elseif ($meeting->modality == 2 || $meeting->modality == 3)
                <span> | Via {{ config('meetings.mode_if_online_types.'.$meeting->mode_if_online) }} - Online</span>
                @else
                    <span class="form-label m-0">Venue or platform not yet set</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 mt-3 mb-3">
                <h5 class="card-header p-0 ">ORDER OF BUSINESS</h5> 
            </div>
        </div>

        <!-- Preliminaries Section -->
        <div class="mb-3">
            <label class="form-label">1. Preliminaries</label>
            <div class="input-group input-group-merge">
            <textarea
        id="preliminaries" 
        class="form-control"
        placeholder="Enter preliminaries."
        aria-label="Enter preliminaries."
        name="preliminaries"
        rows="5"
        {{ $orderOfBusiness->status == 1 || getUserRole() == 'proponent' ? 'disabled' : '' }}
        >
        {{$orderOfBusiness->preliminaries}}</textarea>

                @error('preliminaries')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <!-- Modal -->
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
        <!-- New Business Section -->

        <div class="stick-buttons-group">
            <div class="">
                <button id="enableSelection"  class="action-btn primary">
                    <i class='bx bx-square'></i>
                    <span class="tooltiptext">Group Proposals</span>
                </button>
                <button id="cancelSelection" class="action-btn danger"> 
                    <i class='bx bx-revision' ></i>
                    <span class="tooltiptext">Cancel</span>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">2. New Business</label>

            @php 
                $counter = 1; 
                $actionColors = ['secondary', 'success', 'warning', 'danger', 'info']; 
                $noProposals = collect($categorizedProposals)->flatten()->isEmpty();

                $allProposalIds = collect($categorizedProposals)->flatten()->pluck('id');
            @endphp

            <!-- Loop through proposals and display tables -->
            @foreach ($matters as $type => $title)
                @if ($categorizedProposals[$type]->count() > 0)
                    <div class="table-responsive text-nowrap mb-4">
                        <table class="table table-bordered sortable" id="oobTable">
                            <thead>
                                <tr class="" style="background-color: var(--bs-primary-bg-subtle) !important; border: 1px solid #9B9DFF !important;">
                                    <th colspan="5" class="p-4 text-primary">{{ $title }}</th>
                                </tr>
                                <tr>
                                    <th style="width: 50px;">No.</th>
                                    <th style="width: 700px;">Title of the Proposal</th>
                                    <th style="width: 200px;">Presenters</th>
                                    <th style="width: 150px;">Requested Action</th>
                                    <th style="width: 100px;">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($categorizedProposals[$type]->count() > 0)
                                    @foreach ($categorizedProposals[$type] as $proposal)
                                    @php $group = $proposal->group_proposal_id; @endphp

                                    @if (!$proposal->group_proposal_id)
                                        <tr class="selectable-row" data-id="{{ $proposal->proposal->id }}">
                                            <td><span class="matter_no">2</span>.<span class="order_no">{{$proposal->order_no ? $proposal->order_no : $counter}}</span></td>
                                            <td>
                                                <div style="min-width: 300px; max-width: 700px; white-space: wrap; ">
                                                    <a  href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }}" >{{ $proposal->proposal->title }}</a>
                                                </div>
                                            </td>
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
                                                <span class="align-items-center d-flex gap-2"> 
                                                    <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal->proposal->action] ?? 'primary' }}'></i>
                                                    {{ config('proposals.requested_action.'.$proposal->proposal->action) }}
                                                </span>
                                        
                                            </td>
                                            <td>
                                                @if($proposal->files->count() > 0)
                                                    <button class="btn btn-sm btn-primary d-flex gap-2 view-files"
                                                            data-files="{{ json_encode($proposal->files) }}" 
                                                            data-title="{{ $proposal->title }}">
                                                        <i class='bx bx-file'></i> VIEW FILES
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                                        <i class='bx bx-file'></i> NO FILES
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        <tr data-id="{{$proposal->group_proposal_id}}">     
                                            <tr>
                                                <td>2.{{$proposal->proposal_group?->order_no}}</td>
                                                <td colspan="4">{{ $proposal->proposal_group?->group_title }}</td>
                                            </tr>  
                                            @if($group == $proposal->group_proposal_id)
                                                <tr class="selectable-row" data-id="{{ $proposal->proposal->id }}">
                                                <td><span class="matter_no">2</span>.<span class="order_no">{{ $proposal->proposal_group?->order_no }}.{{$proposal->order_no ? $proposal->order_no : $counter}}</span></td>
                                                <td>
                                                    <div style="min-width: 300px; max-width: 700px; white-space: wrap; ">
                                                        <a  href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }}" >{{ $proposal->proposal->title }}</a>
                                                    </div>
                                                </td>
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
                                                    <span class="align-items-center d-flex gap-2"> 
                                                        <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal->proposal->action] ?? 'primary' }}'></i>
                                                        {{ config('proposals.requested_action.'.$proposal->proposal->action) }}
                                                    </span>
                                            
                                                </td>
                                                <td>
                                                    @if($proposal->files->count() > 0)
                                                        <button class="btn btn-sm btn-primary d-flex gap-2 view-files"
                                                                data-files="{{ json_encode($proposal->files) }}" 
                                                                data-title="{{ $proposal->title }}">
                                                            <i class='bx bx-file'></i> VIEW FILES
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                                            <i class='bx bx-file'></i> NO FILES
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endif
                                        </tr>
                                    @endif
                                    @php $counter++; @endphp
                                    @endforeach
                                @else
                                    <tel
                                        <td colspan="6" class="p-4">
                                            <div class="alert alert-warning m-0" role="alert">
                                                <i class="bx bx-info-circle"></i> No proposals for endorsement at the moment.
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach
            <script>
                var postedToAgendaProposalIDS = @json($allProposalIds);
            </script>
        </div>@php 
    $mainCounter = 1; 
    $actionColors = ['secondary', 'success', 'warning', 'danger', 'info']; 
    $noProposals = collect($categorizedProposals)->flatten()->isEmpty();
    $allProposalIds = collect($categorizedProposals)->flatten()->pluck('id');
@endphp

@foreach ($matters as $type => $title)
    @if ($categorizedProposals[$type]->count() > 0)
        <div class="table-responsive text-nowrap mb-4">
            <table class="table table-bordered sortable" id="oobTable">
                <thead>
                    <tr style="background-color: var(--bs-primary-bg-subtle) !important; border: 1px solid #9B9DFF !important;">
                        <th colspan="5" class="p-4 text-primary">{{ $title }}</th>
                    </tr>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th style="width: 700px;">Title of the Proposal</th>
                        <th style="width: 200px;">Presenters</th>
                        <th style="width: 150px;">Requested Action</th>
                        <th style="width: 100px;">File</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorizedProposals[$type] as $proposal)
                        @php 
                            $group = $proposal->group_proposal_id;
                        @endphp

                        @if (!$group)
                            {{-- Individual Proposal --}}
                            <tr class="selectable-row" data-id="{{ $proposal->proposal->id }}">
                                <td>2.{{ $mainCounter }}</td>
                                <td>
                                    <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }}">
                                        {{ $proposal->proposal->title }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($proposal->proponentsList as $proponent)
                                            <div class="d-flex gap-3 align-items-center">
                                                <img class="rounded-circle avatar avatar-sm pull-up" src="{{ $proponent->image ?? '/default-avatar.png' }}" alt="Avatar">
                                                <span>{{ $proponent->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="align-items-center d-flex gap-2"> 
                                        <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal->proposal->action] ?? 'primary' }}'></i>
                                        {{ config('proposals.requested_action.'.$proposal->proposal->action) }}
                                    </span>
                                </td>
                                <td>
                                    @if($proposal->files->count() > 0)
                                        <button class="btn btn-sm btn-primary d-flex gap-2 view-files"
                                                data-files="{{ json_encode($proposal->files) }}" 
                                                data-title="{{ $proposal->title }}">
                                            <i class='bx bx-file'></i> VIEW FILES
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                            <i class='bx bx-file'></i> NO FILES
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @php $mainCounter++; @endphp

                        @else
                            {{-- Grouped Proposal (First occurrence) --}}
                            @if (!isset($groupSeen[$group]))
                                @php
                                    $groupSeen[$group] = true;
                                    $subCounter = 1;
                                @endphp
                                <tr>
                                    <td>2.{{ $mainCounter }}</td>
                                    <td colspan="4"><strong>{{ $proposal->proposal_group?->group_title }}</strong></td>
                                </tr>
                            @endif

                            {{-- Grouped Sub-Proposals --}}
                            <tr class="selectable-row" data-id="{{ $proposal->proposal->id }}">
                                <td>2.{{ $mainCounter }}.{{ $subCounter }}</td>
                                <td>
                                    <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }}">
                                        {{ $proposal->proposal->title }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($proposal->proponentsList as $proponent)
                                            <div class="d-flex gap-3 align-items-center">
                                                <img class="rounded-circle avatar avatar-sm pull-up" src="{{ $proponent->image ?? '/default-avatar.png' }}" alt="Avatar">
                                                <span>{{ $proponent->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="align-items-center d-flex gap-2"> 
                                        <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal->proposal->action] ?? 'primary' }}'></i>
                                        {{ config('proposals.requested_action.'.$proposal->proposal->action) }}
                                    </span>
                                </td>
                                <td>
                                    @if($proposal->files->count() > 0)
                                        <button class="btn btn-sm btn-primary d-flex gap-2 view-files"
                                                data-files="{{ json_encode($proposal->files) }}" 
                                                data-title="{{ $proposal->title }}">
                                            <i class='bx bx-file'></i> VIEW FILES
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                            <i class='bx bx-file'></i> NO FILES
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @php $subCounter++; @endphp
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endforeach

        
        @if(session('isSecretary'))
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <button type="submit" class="btn btn-primary d-flex gap-2" id="saveOOBBtn" {{$orderOfBusiness->status == 1 ? 'disabled' : ''}}>
                    <i class='bx bx-save' ></i>
                    <span class="text-nowrap">Save Changes</span>
                </button> 
                <button type="buttton" class="btn btn-success d-flex gap-2" id="disseminateOOBBtn" data-id="{{encrypt($orderOfBusiness->id)}}" data-action = "{{ route(getUserRole().'.dissemenate.order_of_business', ['level' => $meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}" {{$orderOfBusiness->status == 1 ? 'disabled' : ''}}>
                    <i class='bx bx-send'></i>
                    <span class="text-nowrap">Disseminate OOB</span>
                </button> 
            </div>
        @endif
    </form>
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

    <!-- CREATE GROUP MODAL -->
    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST"  action="{{ route('save_proposal_group', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}" id="groupFrm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="groupModalLabel">Group Selected Proposals</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="orderNo">Order No:<span class="ms-1 text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span id="" class="input-group-text">
                                    <i class='bx bx-objects-horizontal-left' ></i>
                                </span>
                                <input
                                    type="number" id="orderNo" name="order_no" class="form-control" placeholder="Order number (ex: 2.1)"  required
                                />
                            </div>
                        </div>
                        <div class="">
                            <label class="form-label" for="orderNo">Enter Group Proposal Title<span class="ms-1 text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <!-- <span id="" class="input-group-text">
                                    <i class='bx bx-grid-alt' ></i>
                                </span> -->
                                <textarea name="group_title" id="group_title" rows="4" placeholder="Enter group proposal title" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="saveGroup" class="btn btn-primary">Save Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
   $(document).ready(function () {

       // Initialize Sortable.js
        $("#oobTable tbody").each(function () {
            new Sortable(this, {
                animation: 150,
                handle: "td",
                ghostClass: "sortable-ghost",
                onEnd: function (evt) {
                    updateOrder();
                }
            });
        });

        function updateOrder() {
            let order = [];
            $("#oobTable tbody tr").each(function (index) {
                let proposalId = $(this).data("id");
                order.push({ id: proposalId, position: index + 1 });

                // Update the UI immediately
                $(this).find("td:first .order_no").text(index + 1);
            });

            console.log(order); // Debugging (Remove later)

            // Send AJAX request to update the database
            $.ajax({
                url: "{{ route('update_proposal_order', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}",
                method: "POST",
                data: {
                    orderData: order,
                    _token: "{{ csrf_token() }}" // CSRF token for security
                },
                success: function (response) {
                    console.log("Order updated successfully!", response);
                },
                error: function (xhr, status, error) {
                    console.error("Error updating order:", error);
                }
            });
        }

        let selectionEnabled = false;
        let selectedProposalRows = [];

        // Enable Selection
        $("#enableSelection").click(function (e) {
            e.preventDefault();
            selectionEnabled = true;
            $("#enableSelection, #cancelSelection").addClass("active");
        });

        // Cancel Selection
        $("#cancelSelection").click(function (e) {
            e.preventDefault();
            selectionEnabled = false;
            selectedProposalRows = [];
            $(".selectable-row").removeClass("selected-row");
            $("#enableSelection, #cancelSelection").removeClass("active"); 
        });

        // Row Click Selection
        $(".selectable-row").click(function (e) {
            if (!selectionEnabled) return;

            let rowId = $(this).attr("data-id");

            if (selectedProposalRows.includes(rowId)) {
                selectedProposalRows = selectedProposalRows.filter(id => id !== rowId);
                $(this).removeClass("selected-row");
            } else {
                selectedProposalRows.push(rowId);
                $(this).addClass("selected-row");
            }
            console.log(selectedProposalRows);
        });

        // Right-Click to Open Modal
        $(".selectable-row").contextmenu(function (e) {
            if (!selectionEnabled || selectedProposalRows.length === 0) return;

            e.preventDefault();
            let modal = new bootstrap.Modal($("#groupModal")[0]);
            modal.show();
        });

        // Save Group
        $("#saveGroup").click(function () {
            let groupFrm = $("#groupFrm");
            var actionUrl = groupFrm.attr("action");

            // Create FormData object
            var formData = new FormData(groupFrm[0]);


            // Append selectedProposalRows properly
            selectedProposalRows.forEach((proposalId, index) => {
                formData.append(`proposals[${index}]`, proposalId);
            });

            $.ajax({
                method: "POST",
                url: actionUrl,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if(response.type == 'success'){
                        alert("Group saved!");
                        location.reload(); // Refresh to reflect changes
                    }
                    alert(response);    
                    console.log(response);
                },
                error: function (xhr) {
                    alert("Error: " + (xhr.responseJSON ? xhr.responseJSON.error : "Unknown error"));
                }
            });

            selectedProposalRows = [];
            $(".selectable-row").removeClass("selected-row");
            $("#groupModal").modal("hide");
        });


    });

</script>

<script src="{{asset('assets/js/orderOfBusiness.js')}}"></script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection
