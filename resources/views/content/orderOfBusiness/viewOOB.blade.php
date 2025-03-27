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

<!-- Modal Upload Previous Minutes -->
<div class="modal fade" id="previousMinModal" tabindex="-1" aria-labelledby="previousMinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">UPLOAD PREVIOUS MINUTES</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="">
                <form id="uploadMinutesForm" enctype="multipart/form-data">
                    @csrf
                    <label for="previous_minutes" class="form-label">Previous Minute File</label>
                    <input type="file" name="previous_minutes" id="previous_minutes" class="form-control" required>
                    <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
                <div id="uploadMessage" class="mt-2"></div>

            </div>
        </div>
    </div>
</div>







<div class="card p-4">

    <div class="mt-3 mb-3">
        <button type="button" id="exportOOB" class="btn btn-secondary d-flex gap-2 {{$orderOfBusiness->status == 0 ? 'd-none' : ''}}">
            <a href="{{ route('oob.export.pdf', ['level' => $orderOfBusiness->meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}" class="text-white d-flex align-items-center gap-2" target="_blank"><i class='bx bx-export'></i> Export OOB</a>
        </button>
    </div>
    
    @if (session('isSecretary'))
    <form action="{{ route(getUserRole().'.order_of_business.save', ['level' => $meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}" method="post" id="oobFrm" meeting-id="{{encrypt($orderOfBusiness->meeting->id)}}">
    @endif
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

                <a href="{{$meeting->link ? $meeting->link : '/#'}}"  class="text-primary m-0"><i class='bx bx-link me-1'></i>Click Meeeting Link</a>
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
            <div class="d-flex justify-content-between mb-2 gap-2">
            <label class="form-label">1. Preliminaries</label>
            
            <div class="d-flex align-items-center gap-2">
                @if (in_array(auth()->user()->role, [3, 4, 5]))
                @if(empty($orderOfBusiness->previous_minutes))
                <button class="btn btn-sm btn-primary d-flex align-items-center gap-2" id="openMinutesModal">
                    <i class='bx bx-upload'></i>
                    Upload Previous Minutes
                </button>
                    @endif
    
                <button class="btn btn-sm btn-primary d-flex align-items-center gap-2" id="openAttendanceModal">
                    <i class='bx bx-upload'></i>
                    Upload Previous Attendance
                </button>
                {{-- Show View Button only if there is a file --}}
                 @if(!empty($orderOfBusiness->previous_minutes))
                <a href="#" target="_blank" class="btn btn-sm btn-success d-flex align-items-center gap-2" id="viewButton" style="display: none;">
                    <i class='bx bx-file'></i>
                    View Previous Minutes
                </a>
                @endif

                {{-- EDIT FILE --}}
                @if (in_array(auth()->user()->role, [3, 4, 5]))
                <button type="button" class="btn btn-sm btn-warning d-flex align-items-center gap-2" id="editMinutesButton" style="display: none;">
                    <i class='bx bx-edit'></i> Edit File
                </button>
                @endif

            </div>
         @endif
        
            
        </div>
        
            <div class="input-group input-group-merge">
            <textarea
                id="preliminaries" 
                class="form-control"
                placeholder="Enter preliminaries."
                aria-label="Enter preliminaries."
                name="preliminaries"
                rows="6"
                @if(session('isProponent') ||(session('isSecretary') && session('secretary_level') != $meeting->getMeetingCouncilType())) 
                    disabled 
                @endif
            >    {{$orderOfBusiness->preliminaries}}
            </textarea>


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
        @if (session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
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
        @endif


        <div class="mb-3">
            <label class="form-label">2. New Business</label>

            @php 
                $counter = 1; 
                $groupCounter = 1;
                $actionColors = ['secondary', 'success', 'warning', 'danger', 'info']; 
                $noProposals = collect($categorizedProposals)->flatten()->isEmpty();
                $proposalKey = match ($meeting->getMeetingLevel()) {
                    'Local' => 'local_proposal_id',
                    'University' => 'university_proposal_id',
                    'BOR' => 'board_proposal_id',
                    default => ''
                };

                $allProposalIds = collect($categorizedProposals)->flatten()->pluck($proposalKey);
            @endphp

            @foreach ($matters as $type => $title)
                @php 
                    // Group proposals and standalone proposals together based on order_no
                    $allProposals = collect();

                    // Add standalone proposals to collection
                    foreach ($categorizedProposals[$type]->whereNull('group_proposal_id') as $proposal) {
                        $allProposals->push([
                            'type' => 'individual',
                            'order_no' => $proposal->order_no,
                            'data' => $proposal
                        ]);
                    }

                    // Add grouped proposals to collection
                    foreach ($categorizedProposals[$type]->whereNotNull('group_proposal_id')->groupBy('group_proposal_id') as $groupID => $proposals) {
                        $groupOrderNo = $proposals->first()->proposal_group->order_no ?? 9999;
                        $allProposals->push([
                            'type' => 'group',
                            'order_no' => $groupOrderNo,
                            'group_id' => $groupID,
                            'data' => $proposals
                        ]);
                    }

                    // Sort by order_no
                    $allProposals = $allProposals->sortBy('order_no');
                @endphp
                
                @if ($categorizedProposals[$type]->count() > 0)
                    <div class="table-responsive text-nowrap mb-4">
                        <table class="table table-bordered sortable" id="{{ session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()) ? 'oobTable' :  ''}}">
                            <thead>
                                <tr style="background-color: var(--bs-primary) !important; border-color: var(--bs-primary)  !important;">
                                    <th colspan="5" class="p-4 text-white">{{ $title }}</th>
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
                                @foreach ($allProposals as $proposal)
                                    @if ($proposal['type'] === 'individual')
                                        <tr class="selectable-row" data-id="{{ $proposal['data']->proposal->id }}">
                                            <td>2.<span class="order_no">{{ $counter }}</span></td>
                                            <td>
                                                <span>{{ $proposal['data']->proposal->title }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-3">
                                                    @foreach ($proposal['data']->proposal->proponents ?? [] as $proponent)
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img class="rounded-circle avatar-sm" src="{{ $proponent->image ?? '/default-avatar.png' }}" alt="Avatar">
                                                            <span>{{ $proponent->name }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <span class="d-flex gap-2 align-items-center">
                                                    <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal['data']->proposal->action] ?? 'primary' }}'></i>
                                                    {{ config('proposals.requested_action.'.$proposal['data']->proposal->action) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($proposal['data']->proposal->files->isNotEmpty())
                                                    <button class="btn btn-sm btn-secondary view-files d-flex gap-2" data-files="{{ json_encode($proposal['data']->proposal->files) }}" data-title="{{ $proposal['data']->proposal->title }}">
                                                        <i class='bx bx-file'></i> VIEW FILES
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                                        <i class='bx bx-file'></i> NO FILES
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $counter++; @endphp
                                    @else
                                    <tr class="tr-group selectable-row group position-relative" data-id="{{ $proposal['data']->first()->proposal_group->id }}">
                                        <td>2.<span class="order_no">{{ $counter }}</span></td>
                                        <td colspan="4">
                                            <strong>{{ $proposal['data']->first()->proposal_group->group_title ?? 'Group Proposal' }}</strong>

                                            <!-- Dropdown inside the row (hidden by default) -->
                                                 <!-- New Business Section -->
                                            @if (session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
                                                <div class="dropdown position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%);">
                                                    <button class="btn btn-sm btn-warning dropdown-toggle d-none group-menu-btn" type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><button class="dropdown-item ungroup-btn">Ungroup</button></li>
                                                        <li><button class="dropdown-item edit-group-btn">Edit</button></li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                        @foreach ($proposal['data'] as $groupedProposal)
                                            <tr class="selectable-row group-items" data-id="{{ $groupedProposal->proposal->id }}">
                                                <td class="ps-5 pe-1">
                                                    <span class="order_no">2.{{ $counter }}.{{ $groupCounter }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $groupedProposal->proposal->title }}</span>
                                                </td>
                                                <td>  
                                                    <div class="d-flex flex-column gap-3">
                                                        @foreach ($groupedProposal->proposal->proponents ?? [] as $proponent)
                                                            <div class="d-flex align-items-center gap-3">
                                                                <img class="rounded-circle avatar-sm" src="{{ $proponent->image ?? '/default-avatar.png' }}" alt="Avatar">
                                                                <span>{{ $proponent->name }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="d-flex gap-2 align-items-center">
                                                        <i class='bx bx-up-arrow-circle text-{{ $actionColors[$groupedProposal->proposal->action] ?? 'primary' }}'></i>
                                                        {{ config('proposals.requested_action.'.$groupedProposal->proposal->action) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($groupedProposal->proposal->files->isNotEmpty())
                                                        <button class="btn btn-sm btn-secondary view-files d-flex gap-2" data-files="{{ json_encode($groupedProposal->proposal->files) }}" data-title="{{ $groupedProposal->proposal->title  }}">
                                                            <i class='bx bx-file'></i> VIEW FILES
                                                        </button>
                                                    @else 
                                                        <button class="btn btn-sm btn-danger d-flex gap-2" disabled>
                                                            <i class='bx bx-file'></i> NO FILES
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php $groupCounter++; @endphp
                                        @endforeach
                                        @php $counter++; $groupCounter = 1; @endphp
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach


            <script>
                var postedToAgendaProposalIDS = @json($allProposalIds);
            </script>
        </div>
        
        @if(session('isSecretary') && (session('secretary_level') == $meeting->getMeetingCouncilType()))
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <button type="submit" class="btn btn-primary d-flex gap-2" id="saveOOBBtn">
                    <i class='bx bx-save' ></i>
                    <span class="text-nowrap">Save Changes</span>
                </button> 
                <button type="buttton" class="btn btn-success d-flex gap-2" id="disseminateOOBBtn" data-id="{{encrypt($orderOfBusiness->id)}}" data-action = "{{ route(getUserRole().'.dissemenate.order_of_business', ['level' => $meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}">
                    <i class='bx bx-send'></i>
                    <span class="text-nowrap">{{$orderOfBusiness->status == 1 ? 'Redisseminate OOB' : 'Disseminate OOB'}}</span>
                </button> 
            </div>
        @endif
    @if (session('isSecretary'))
    </form>
    @endif

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
                <form method="POST" action="{{ route('save_proposal_group', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}" id="groupFrm">
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
                                    <i class='bx bx-objects-horizontal-left'></i>
                                </span>
                                <input type="number" id="orderNo" name="order_no" class="form-control" placeholder="Order number (ex: 2.1)" required />
                            </div>
                        </div>
                        <div class="">
                            <label class="form-label" for="orderNo">Enter Group Proposal Title<span class="ms-1 text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <textarea name="group_title" id="group_title" rows="4" placeholder="Enter group proposal title" class="form-control"></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="group_id" name="group_id" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="saveGroup" class="btn btn-primary">Save Group</button>
                        <button type="button" id="updateGroup" class="btn btn-primary" style="display: none;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
     // SHOW PROPOSAL FILE
   $(document).on('click', '.view-files', function (e) {
        e.preventDefault();
        var files = $(this).data("files");
        var title = $(this).data("title");

        console.log(files);

        if (!files || files.length === 0) {
            $("#modalFiles").html('<p class="text-danger">No files available.</p>');
        } else {
            let fileListHtml = `
                <div class="">
                    <div class="d-flex flex-column">
                        <span class="form-label">Title:</span>
                        <h6 id="modal-title">${title || 'No Title Available'}</h6>
                    </div>
                    <div class="">
                        <span class="form-label">Files:</span>
                        <div class="d-flex flex-column gap-2 mt-2">
            `;

            $.each(files, function (index, fileObj) {
                if(fileObj.is_active == true){
                    fileListHtml += `
                    <a href="#" class="form-control d-flex align-items-center gap-2 view-file-preview" style="text-transform: none;"
                    data-bs-toggle="modal" 
                    data-bs-target="#fileModal"
                    data-file-url="/storage/proposals/${fileObj.file}" >
                        <span>${fileObj.order_no}. </span><i class='bx bx-file-blank'></i><span>${fileObj.file}</span>
                    </a>`;
                }
            });

            fileListHtml += `</div></div></div>`;
            $("#modalFiles").html(fileListHtml);
        }

        var myModal = new bootstrap.Modal(document.getElementById('proposalFIleModal'));
        myModal.show();
    });

    $(document).on('click', '.view-file-preview', function (e) {
        e.preventDefault();
        const fileUrl = $(this).data('file-url');
        $('#fileIframe').attr('src', fileUrl);

        var fileModal = new bootstrap.Modal(document.getElementById('fileModal'));
        fileModal.show();
    });

    $('#fileModal').on('show.bs.modal', function () {
        $('#proposalFIleModal').addClass('d-block');
    });

    $('#fileModal').on('hidden.bs.modal', function () {
        $('#proposalFIleModal').removeClass('d-block');
        $('#proposalFIleModal').modal('show');
    });

    $('#proposalFIleModal').on('hidden.bs.modal', function () {
        setTimeout(function() {
            if ($('.modal-backdrop').length > 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            }
        }, 200); 
    });


    
    $(document).ready(function () {
        // Right-click event for group rows
        $(document).on("contextmenu", ".tr-group", function (event) {
            event.preventDefault(); // Prevent default menu

            // Hide all other dropdowns
            $(".group-menu-btn").addClass("d-none");

            // Show the dropdown menu inside the clicked row
            let dropdown = $(this).find(".group-menu-btn");
            dropdown.removeClass("d-none").dropdown("toggle");
        });

        // Hide menu when clicking elsewhere
        $(document).on("click", function () {
            $(".group-menu-btn").addClass("d-none");
        });

        // Handle 'Ungroup' click
        $(".ungroup-btn").click(function (e) {
            e.preventDefault();
            let groupId = $(this).closest(".tr-group").data("id");
           
            $.ajax({
                url: "{{ route('ungroup_proposal', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}",
                method: "POST",
                data: {
                    group_id: groupId,
                    _token: "{{ csrf_token() }}" // CSRF token for security
                },
                success: function (response) {
                    if (response.type === 'success') {
                        showAlert(response.type, response.title, response.message);
                        location.reload(); // Refresh to reflect changes
                    } else {
                        showAlert('danger', 'Error', response.message);
                    }
                },
                error: function (xhr) {
                    showAlert('danger', 'Error', (xhr.responseJSON ? xhr.responseJSON.error : "Unknown error"));
                }
            });
        });

        // Handle 'Edit' click
        $(".edit-group-btn").click(function (e) {
            e.preventDefault();
            let groupId = $(this).closest(".tr-group").data("id");
            let groupTitle = $(this).closest(".tr-group").find("strong").text();
            let orderNo = $(this).closest(".tr-group").find(".order_no").text();

            // Set the values in the modal
            $("#groupModal #group_title").val(groupTitle);
            $("#groupModal #orderNo").val(orderNo);
            $("#groupModal #group_id").val(groupId);
            $("#groupModal #saveGroup").hide();
            $("#groupModal #updateGroup").show();

            // Show the modal
            let modal = new bootstrap.Modal($("#groupModal")[0]);
            modal.show();
        });

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
                    if (response.type == 'success') {
                        showAlert(response.type, response.title, response.message);
                        location.reload(); // Refresh to reflect changes
                    } else {
                        showAlert('danger', 'Error', response.message);
                    }
                    console.log(response);
                },
                error: function (xhr) {
                    showAlert('danger', 'Error', (xhr.responseJSON ? xhr.responseJSON.error : "Unknown error"));
                }
            });

            selectedProposalRows = [];
            $(".selectable-row").removeClass("selected-row");
            $("#groupModal").modal("hide");
        });

        // Update Group
        $("#updateGroup").click(function (e) {
            e.preventDefault();
            let groupFrm = $("#groupFrm");
            var actionUrl = "{{ route('update_proposal_group', ['level' => $orderOfBusiness->meeting->getMeetingLevel()]) }}";

            // Create FormData object
            var formData = new FormData(groupFrm[0]);

            $.ajax({
                method: "POST",
                url: actionUrl,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.type == 'success') {
                        showAlert(response.type, response.title, response.message);
                        location.reload(); // Refresh to reflect changes
                    } else {
                        showAlert('danger', 'Error', response.message);
                    }
                    console.log(response);
                },
                error: function (xhr) {
                    showAlert('danger', 'Error', (xhr.responseJSON ? xhr.responseJSON.error : "Unknown error"));
                }
            });

            $("#groupModal").modal("hide");
        });

        checkPreviousMinutes();

            function checkPreviousMinutes() {
                let meetingId = $("input[name='meeting_id']").val();

                $.ajax({
                    url: "{{ route('get.previous.minutes', ':meeting_id') }}".replace(':meeting_id', meetingId),
                    type: "GET",
                    success: function(response) {
                        if (response.success && response.previous_minutes) {
                            console.log("Previous Minutes Found:", response.previous_minutes);
                            $("#openMinutesModal").hide();
                            $("#viewButton").attr("href", "/storage/previous_minutes/" + response.previous_minutes).show();
                            $("#editMinutesButton").show();

                        } else {
                            console.log("No Previous Minutes Found");

                            // Show upload button & hide view button
                            $("#openMinutesModal").show();
                            $("#viewButton").hide();
                            $("#editMinutesButton").hide();

                        }
                    }
                });
            }

                $("#editMinutesButton").click(function(e) {
                e.preventDefault();
                $("#editMinutesModal").modal("show");
                });

        $('#editMinutesForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route(getUserRole().'.upload.minutes') }}", 
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        toastr.info("Uploading new file...", "", { timeOut: 0, extendedTimeOut: 0 });
                    },
                    success: function(response) {
                        toastr.clear();
                        if (response.success) {
                            toastr.success(response.message);

                            $('#editMinutesModal').modal('hide');
                            $("#editMinutesForm")[0].reset();
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(jqXHR) {

                        console.log(jqXHR);
                        // let errorMessage = "An error occurred. Please try again.";
                        // if (xhr.responseJSON && xhr.responseJSON.message) {
                        //     errorMessage = xhr.responseJSON.message;
                        // }
                        // toastr.error(errorMessage);
                    }
                });
            });
            // $('#editMinutesForm').submit();

        $("#openMinutesModal").click(function(e) {
            e.preventDefault();
            $("#previousMinModal").modal("show");
        });

        $('#uploadMinutesForm').on('submit', function(e) {
                e.preventDefault(); 

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route(getUserRole().'.upload.minutes') }}", 
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        toastr.info("Uploading previous minutes...", "", { timeOut: 0, extendedTimeOut: 0 }); // Show infinite loading
                    },
                    success: function(response) {
                        toastr.clear();
                        if (response.success) {
                            toastr.success(response.message);

                            $('#previousMinModal').modal('hide');
                            $("#uploadMinutesForm")[0].reset();
                            $("#openMinutesModal").remove();

                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "An error occurred. Please try again.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);

                    }
                });
            });



    });
</script>

<script src="{{asset('assets/js/orderOfBusiness.js')}}"></script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection
