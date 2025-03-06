@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Proposal')

@section('content')
<!-- <h4 class="py-3 mb-4">
  Typography
</h4> -->
<div class="d-flex align-items-center justify-content-between">
    <div class="bread-crumbs overflow-auto" style="max-width: 100%; white-space: nowrap;">
        <h5>Dashboard</h5>
        <div class="divider"></div>
        <a href="/">
            <i class='bx bx-home-alt' ></i>
        </a>
        <i class='bx bx-chevron-right' ></i>
        <a href="{{route(    getUserRole().'.proposals')}}">Meetings</a>
        <i class='bx bx-chevron-right' ></i>
        <a href="#">My Proposals</a>
        <i class='bx bx-chevron-right' ></i>
        <a href="#">Edit Proposal</a>
    </div>
</div>

<div class="p-0">
    <div class="row">
        <div class="col col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route(getUserRole().'.proposal.edit.save', ['proposal_id' => encrypt($proposal->id)]) }}" method="post" id="editProposalFrm">
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <h6 class="m-0">PROPOSAL DETAILS </h6>                       
                            <span class="badge bg-label-primary">{{ config('proposals.status.'.$proposal->status) }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="title">Proposal Title <span class="ms-1 text-danger">*</span></label>
                              
                            <textarea
                                id="title"
                                name="title"
                                class="form-control"
                                placeholder="Enter title"
                                aria-label="Enter title"
                                required
                                rows="2"
                            >{{$proposal->title}}</textarea>
                        </div>
                        <div class="col-md-6 c-field-p w-100">
                            <div class="mb-3" id="subTypeContainer" style="">
                                <label class="form-label" for="addProponent">Add Proponents (Optional)</label>
                                <div class="input-group input-group-merge">
                                    <span id="title-icon" class="input-group-text"><i class='bx bx-user'></i></span>
                                    <input type="text" class="form-control" id="addProponent" name="addProponent" autocomplete="off" placeholder="Search for users...">
                                </div>
                                @error('proponent')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="search-drop-card card" style="display: none;">
                                <div class="card-body">
                                    <ul class=""></ul>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="subTypeContainer" style="">
                            <label class="form-label" for="proponents">Proponent/s <span class="ms-1 text-danger">*</span></label>
                            <div class="form-control proponent-con" style="">
                                <input type="text" class="form-control mb-3" value="{{$proposal->employee_id}}" id="proponents" name="proponents" hidden>
                                <ul class="" id="proponentListCon">
                                    @foreach ($proposal->proponentsList as $proponent)
                                        <li data-id="{{$proponent->employee_id}}" data-name="{{$proponent->name}}" data-email="{{$proponent->email}}" data-image="{{$proponent->image}}" id="primaryProponent">
                                            <div class="d-flex justify-content-between align-items-center ms-2 me-2">
                                                <div class="d-flex justify-content-start align-items-center ">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3">
                                                            <img src="{{$proponent->image}}" alt="Avatar" class="rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <a href="" class="text-heading text-truncate m-0">
                                                            <span class="fw-medium">{{$proponent->name}}</span>
                                                        </a>
                                                        <small>{{$proponent->email}}</small>
                                                    </div>
                                                </div>
                                                @if($proponent->employee_id === session('employee_id'))
                                                    <div class="">
                                                        <small class="badge bg-label-secondary d-flex align-items-center gap-2">
                                                            <i class='bx bx-user-check'></i>Submitter
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="">
                                                        <small class="badge bg-label-danger d-flex align-items-center gap-2 remove" data-id="{{$proponent->employee_id}}"><i class='bx bx-trash'></i>Remove</small>
                                                    </div> 
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach

                                    @php
                                        $selectedProponents = $proposal->proponentsList->map(function ($proponent) {
                                            return [
                                                'id' => $proponent->employee_id,
                                                'name' => $proponent->name,
                                                'email' => $proponent->email,
                                                'image' => $proponent->image,
                                            ];
                                        })->toArray();
                                    @endphp

                                    <script>
                                        window.selectedProponents = @json($selectedProponents); 
                                    </script>
                                </ul>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="sub_type">Type of Matter or Proposal <span class="ms-1 text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span id="matters-icon" class="input-group-text"><i class="bx bx-briefcase"></i></span>
                                <select
                                    class="form-control @error('matter') is-invalid @enderror"
                                    id="matter"
                                    name="matter"
                                    aria-label="Select Matter"
                                    aria-describedby="matters-icon"
                                    required
                                >
                                    <option value="" disabled>Select Type of Matter or Proposal</option>
                                    @foreach (config('proposals.matters') as $key => $value)
                                        <option value="{{ $key }}" {{ $proposal->type === $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('matter')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>           
                        <div class="mb-3">
                            <label class="form-label" for="action">Requested Action <span class="ms-1 text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span id="action-icon" class="input-group-text"><i class="bx bx-task"></i></span>
                                <select
                                    class="form-control @error('action') is-invalid @enderror"
                                    id="action"
                                    name="action"
                                    aria-label="Action to be taken"
                                    aria-describedby="action-icon"
                                >
                                    <option value="" disabled>Select an action</option>
                                    @foreach (config('proposals.requested_action') as $key => $item)
                                        <option value="{{ $key }}" {{ $proposal->action === $key ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('action')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="subTypeContainer" style="{{$proposal->type == 2 ? 'display: block;' : 'display: none;'}}">
                            <label class="form-label" for="sub_type">Sub Type</label>
                            <div class="input-group input-group-merge">
                                <span id="sub-type-icon" class="input-group-text"><i class="bx bx-category-alt"></i></span>
                                <select
                                    name="sub_type"
                                    id="sub_type"
                                    class="form-control @error('sub_type') is-invalid @enderror"
                                    aria-label="Sub-type of proposal"
                                    aria-describedby="sub-type-icon"
                                    required
                                    disabled
                                >
                                    <option value="">Select Sub-type</option>
                                    @foreach (config('proposals.proposal_subtypes') as $key => $subType)
                                        <option value="{{ $key }}" {{ $proposal->sub_type === $key ? 'selected' : '' }}>{{ $subType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('sub_type')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="">PROPOSAL FILE/S</label>
                            <input type="file" id="file-upload" style="display: none;" accept=".pdf, .xls, .xlsx, .csv"
                            >
                            <div class="proposal-file-con mb-2">
                                @php
                                    $countLatestVersion = 0;
                                @endphp
                                @foreach ($proposal->files as $file)
                                    @if ($file->is_active == 1)
                                        @php
                                            $countLatestVersion++;
                                        @endphp
                                        <div class="proposal-file-card">
                                            <div class="proposal-file-card-header">
                                                <span class="custom-badge version">Version {{ $file->version }}</span>
                                                <span class="custom-badge file-status">{{ config('proposals.proposal_file_status.'.$file->file_status ) }}</span>
                                            </div>
                                            <div class="proposal-file-card-body" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#fileModal"
                                                data-file-url="/storage/proposals/{{$file->file}}">
                                                <div class="proposal-file-img">
                                                    <img src="{{ asset('assets/img/icons/document/folder_2.png') }}" alt="">
                                                </div>
                                                <small data-bs-toggle="tooltip" data-bs-placement="top" title="{{ pathinfo($file->file, PATHINFO_FILENAME) . '.' . pathinfo($file->file, PATHINFO_EXTENSION) }}">
                                                    {{ Str::limit(pathinfo($file->file, PATHINFO_FILENAME), 15, '...') . '.' . pathinfo($file->file, PATHINFO_EXTENSION) }}
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 proposal-file-footer">
                                                <button class="btn btn-sm btn-primary resubmit-proposal" data-id="{{ $file->id }}" {{ (!in_array($proposal->status, [2,5,6]) && $proposal->is_edit_disabled && $file->file_status) ? 'disabled' : '' }}>Reupload</button>

                                                <button class="btn btn-sm btn-danger delete-proposal-file"  data-id="{{ $file->id }}" {{ (!in_array($proposal->status, [2,5,6]) && $proposal->is_edit_disabled  && $file->file_status) ? 'disabled' : '' }}>Delete</button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <small class="text-muted text-wrap d-flex gap-2"><strong>Note:</strong><em>Please be cautious when reuploading and deleting a proposal file.</em></small>
                        </div>
                        <div class="">
                            <div class="dropzone-container mb-3">
                                <label class="form-label" for="file">Add Attachment/s</label>
                                <input type="text" class="form-control mb-3" value="" name="proposalFiles" id="proposalInput" hidden>
                                <div class="dropzone mb-2" id="fileDropzone">
                                
                                </div>
                                <span class="text-muted">Accepted formats: .pdf, .xls, .xlsx, .csv only</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary d-flex gap-2" id="updateProposal" {{ (!in_array($proposal->status, [2,5,6]) && $proposal->is_edit_disabled  && $file->file_status) ? 'disabled' : '' }}
                            ><i class='bx bx-save'></i> Save Changes</button>
                            <!-- <button class="btn btn-secondary">Save Changes</button> -->
                        </div>
                    </form>
                </div>
            </div>
        </div> 
        <div class="col w-100">
            <!-- <div class="card" style="height: 100%;"> -->
            <div class="card">
                <div class="">
                    <div class="d-flex justify-content-between gap-2 mb-3 align-items-center p-4 border-bottom">
                        <h6 class="m-0">PROPOSAL STATUS TIMELINE</h6>

                        @php
                            // Get the latest level from logs or default to Local Council (0)
                            $latestLevel = $proposal_logs->max('level') ?? 0;
                            $levels = [
                                0 => 'Local Council',
                                1 => 'University Council',
                                2 => 'BOR',
                                3 => 'Approved by BOR'
                            ];
                            $currentLevel = $levels[$latestLevel] ?? 'Unknown';
                        @endphp

                        <span class="badge bg-label-success">Current Step: {{ $currentLevel }}</span>
                    </div>

                    <div class="d-flex flex-column align-items-center w-100 p-3">
                        <div class="progress-wrapper-3 m-4">
                            @foreach ($levels as $levelKey => $levelName)
                                @php
                                    $hasLogs = $proposal_logs->where('level', $levelKey)->isNotEmpty();
                                    $lastLogs = $proposal_logs->where('level', 2)->where('status', 3)->first();
                                @endphp

                                <div class="c-progress-step {{ $levelKey == 3 ? 'last' : '' }}">
                                    <div class="step-badge {{ $hasLogs || $lastLogs ? 'active' : '' }}">
                                        <small><strong>STEP</strong></small>
                                        <h4 class="m-0">{{ $levelKey + 1 }}</h4>
                                    </div>
                                    <div class="step-title {{ $hasLogs || $lastLogs ? 'active' : '' }}">
                                        <i class='bx bx-objects-horizontal-right'></i>
                                        <h6 class="m-0">{{ strtoupper($levelName) }}</h6>
                                    </div>
                                </div>

                                @foreach ($proposal_logs->where('level', $levelKey) as $log)
                                    @php
                                        // Define log status classes dynamically
                                        $logStatusClass = match ($log->status) {
                                            2, 7 => 'danger',
                                            5, 6 => 'warning',
                                            default => ''
                                        };
                                    @endphp

                                    <div class="c-progress-2">
                                        <div class="log-card {{ $logStatusClass }}">
                                            <div class="card-body p-3">
                                                <h6 class="card-title m-0">
                                                    {{ $log->status == 0 ? 'Submitted the proposal - ' : '' }} 
                                                    {{ config('proposals.status.' . $log->status) }}
                                                </h6>
                                                <hr>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($log->created_at)->format('F j, Y') }}
                                                </small>
                                                @if($log->comments)
                                                    <div class="alert alert-danger" role="alert">
                                                        <p class="card-text">
                                                            <strong class="fst-italic">Comment: </strong>{{ $log->comments }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>  
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Preview File -->
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

<script>
    var proposalStatus = @json(config('proposals.status'));

    var proposalFiles = [];
    let deletedFiles = [];
    let reuploadedFiles = [];
    let proposalInput = $('#proposalInput');

    var myDropzone = new Dropzone("div#fileDropzone", {
        url: "{{route(getUserRole().'.projects.storeMedia')}}",
        paramName: "file",
        maxFilesize: 100,
        addRemoveLinks: true,
        autoProcessQueue: true,
        acceptedFiles: ".pdf, .xls, .xlsx, .csv",
        dictDefaultMessage: `<div class='d-flex gap-2 align-items-center justify-content-center'>
            <i class='bx bx-upload text-primary'></i>
            <h5 class='text-primary m-0'>Choose File</h5>
        </div><br> or <br>Drag and drop your <strong>proposal</strong> file here`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (file, response) {
            file.renamedFileName = response.name;

            proposalFiles.push(response.name);
            console.log(response);
            console.log('Added Files')
            console.log(proposalFiles);
            proposalInput.val(proposalFiles.join('/'));
        },
        removedfile: function (file) {
            var fileName = file.renamedFileName; 

            $.ajax({
                url: "{{route(getUserRole().'.media.delete')}}", 
                type: "POST",
                data: { filename: fileName },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log("File removed:", response);
                },
                error: function (xhr, status, error) {
                    console.error("Error removing file:", error);
                }
            });

            file.previewElement.remove(); // Remove file preview from Dropzone
            proposalFiles = proposalFiles.filter(f => f !== fileName); // Remove file from array
            proposalInput.val(proposalFiles.join('/')); // Update hidden input field
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });

    $(".resubmit-proposal").on('click', function (e) {
        e.preventDefault();
        
        var file_id = $(this).data('id');
        $("#file-upload").data("id", file_id).click(); // Store file ID and trigger file input
        $(this).closest(".file-status").html("Revised");
    });
    
    $("#file-upload").on("change", function (e) {
        var file = e.target.files[0]; // Get selected file
       var file_id = $(this).data('id');

        if (file) {
            reuploadedFiles.push({ file_id, file });
        }

        console.log(reuploadedFiles);
        if (this.files.length > 0) {
            var file_id = $(this).data("id");
            var proposalCard = $(".resubmit-proposal[data-id='" + file_id + "']").closest(".proposal-file-card");

            // Update status to "Revised"
            proposalCard.find(".file-status")
                .css("color", "#96D4C7")
                .html("Revised");

            // Increment version number
            var versionElement = proposalCard.find(".version");
            var currentVersion = parseInt(versionElement.text().replace("Version ", "").trim());
            var newVersion = currentVersion + 1;

            // Update version badge
            versionElement.css("color", "#96D4C7").text("Version " + newVersion);
        }

    });
    
    $(".delete-proposal-file").on('click', function(e){
        e.preventDefault();
        var file_id = $(this).data('id');
    
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                deletedFiles.push(file_id);
                $(this).closest(".proposal-file-card").remove();
                console.log(deletedFiles);
            }
        });
    });

    $("#updateProposal").on('click', function (e) {
        e.preventDefault();

        var proposalFrm = $("#editProposalFrm")[0]; // Get the raw form element
        var formData = new FormData(proposalFrm); // Create FormData from the form

        var actionUrl = $("#editProposalFrm").attr('action');

        // Append deleted files
        deletedFiles.forEach((fileId) => {
            formData.append("deleted_files[]", fileId);
            console.log("Deleted File ID: " + fileId);
        });

        // Append reuploaded files
        reuploadedFiles.forEach(({ file_id, file }) => {
            formData.append(`reuploaded_files[${file_id}]`, file);
            console.log("Reuploaded File ID: " + file_id);
            console.log("Reuploaded File: " + file);
            
        });

        console.log('Form Data:', formData);

        // Send AJAX Request
        $.ajax({
            method: "POST",
            url: actionUrl,
            data: formData,
            processData: false,  
            contentType: false,  
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#updateProposal").html(`<i class='bx bx-loader-alt bx-spin' ></i>
                    <span>Saving Changes...</span>`).prop('disabled', true);
            },
            success: function (response) {
                $("#updateProposal").html(`<i class='bx bx-save'></i>
                    <span>Save Changes</span>`).prop('disabled', false);
                console.log(response);
                showAlert(response.type, response.title, response.message);
                location.reload();
            },
            error: function (xhr, status, error) {
                $("#updateProposal").html(`<i class='bx bx-save'></i>
                    <span>Save Changes</span>`).prop('disabled', false);
                console.log(xhr.responseText);
                let response = JSON.parse(xhr.responseText);
                showAlert("warning", response.title, response.message);
            }
        });
    });

</script>
<!-- <script src="{{asset('assets/js/proposal.js')}}"></script> -->
@endsection