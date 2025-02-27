@extends('layouts/contentNavbarLayout')

@section('title', 'Proposals')

@section('content')

<div class="bread-crumbs overflow-auto" style="max-width: 100%; white-space: nowrap;">
    <h5>Dashboard</h5>
    <div class="divider"></div>
    <a href="/">
        <i class='bx bx-home-alt' ></i>
    </a>
    <i class='bx bx-chevron-right' ></i>
    <a href="">Meetings</a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#">Submit Proposal</a>
</div>

<div class="">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-content fade-bg-wrapper">
                    <div class="fade-bg-con">
                        <img src="{{asset('assets/img/backgrounds/slsu_bg_2.jpeg') }}"  class="img-fluid rounded-top user-timeline-image" alt="user timeline image">
                    </div>
                    <div class="meeting-head-text">
                        <div class="d-flex justify-content-between gap-2 flex-wrap">
                            <h4 class="">{{ config('meetings.quaterly_meetings.2') }} {{ config("meetings.council_types." . ['local_level', 'university_level', 'board_level'][1] . ".{1}") }}
                            2025</h4>
                            <div class="">
                                <span class="btn btn-sm btn-success me-1">Active</span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between  meeting-sub-details">
                            <div class="d-flex flex-wrap gap-3 mb-1">
                                <span class="form-label">Submission: </span>
                                <h6>January - January</h6>
                            </div>
                            <div class="d-flex flex-wrap gap-3 mb-1">
                                <span class="form-label">Meeting Date: </span>
                                <h6>Not yet set</h6>
                            </div>
                        </div>
                    </div>
                    <div class="p-3">

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form method="POST" action="" enctype="multipart/form-data" id="proposalFrm">
        <div class="">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 mb-3">
                <span class="text-muted m-0">Please fill all the required fields <em class="text-danger">*</em></span>
                <button type="submit" class="mt-4 btn btn-primary d-flex gap-2" id="submitProposalBtn">
                    <i class='bx bx-send' ></i>
                    <span class="text-nowrap">Submit Proposal</span> 
                </button>
            </div>
            <div class="row mb-4">
                <!-- Proposal details -->
                <div class="col-md-6">
                    <div class="card mb-4" style="height:100%;">
                        <div class="card-body">
                            <h6 class="text-primary">PROPOSAL DETAILS</h6>
                            <div class="mb-3">
                                <label class="form-label" for="title">Title <span class="ms-1 text-danger">*</span></label>
                                <!-- <div class="input-group input-group-merge d-flex align-items-start">
                                    <span id="title-icon" class="input-group-text"><i class='bx bx-text'></i></span> -->
                                    <textarea
                                        id="title"
                                        name="title"
                                        class="form-control"
                                        placeholder="Enter title"
                                        aria-label="Enter title"
                                        required
                                        rows="3"
                                    ></textarea>
                                <!-- </div> -->
                                @error('title')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
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
                                                <option value="{{ $key }}" >
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
                                       
                                        @foreach (config('proposals.requested_action') as $key => $value)
                                            <option value="{{ $key }}" >
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('action')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3" id="subTypeContainer" style="">
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
                                    >
                                        <option value="">Select Sub-type</option>
                                        @foreach (config('proposals.proposal_subtypes') as $key => $subType)
                                            <option value="{{ $key }}" @selected(old('sub_type') == $key)>{{ $subType }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('sub_type')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4" style="height:100%;">
                        <div class="card-body">
                            <h6 class="text-primary">PROPONENT/S</h6>
                            <div class="col-md-6 c-field-p w-100">
                                <div class="mb-3" id="subTypeContainer" style="">
                                    <label class="form-label" for="addProponent">Add Proponents (Optional)</label>
                                    <div class="input-group input-group-merge">
                                        <span id="title-icon" class="input-group-text"><i class='bx bx-user'></i></span>
                                        <input type="text" class="form-control" id="addProponent" name="addProponent" autocomplete="off" placeholder="Search for users...">
                                    </div>
                                    <div class="d-flex mt-2">
                                        <small class="text-muted d-flex gap-2">
                                            <strong class="text-primary">Note:</strong>
                                            <p>You can only add a joint proponent or with the same role as yours.</p>
                                        </small>
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
                                    <input type="text" class="form-control" value="" id="proponents" name="proponents" hidden>
                                    <ul class="" id="proponentListCon">
                                        <li data-id="" data-name="" data-email="" data-image="" id="primaryProponent">
                                            <div class="d-flex justify-content-between align-items-center ms-2 me-2">
                                                <div class="d-flex justify-content-start align-items-center ">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3">
                                                            <img src="" alt="Avatar" class="rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <a href="" class="text-heading text-truncate m-0">
                                                            <span class="fw-medium"> (You)</span>
                                                        </a>
                                                        <small></small>
                                                    </div>
                                                </div>
                                                <div class="">
                                                    <small class="badge bg-label-secondary d-flex align-items-center gap-2">
                                                        <i class='bx bx-user-check'></i>Submitter
                                                    </small>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-primary">PROPOSAL FILES</h6>
                        <div class="dropzone-container mb-3">
                            <label class="form-label" for="file">Proposal File/s <span class="ms-1 text-danger">*</span></label>
                            <input type="text" class="form-control mb-3" value="" name="proposalFiles" id="proposalInput" hidden>
                            <div class="dropzone mb-2" id="fileDropzone">
                            
                            </div>
                            <span class="text-muted">Accepted formats: .pdf only</span>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    var proposalFiles = [];
    let proposalInput = $('#proposalInput');

    var myDropzone = new Dropzone("div#fileDropzone", {
        url: "/jiodjfsjdf",
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
                url: "", 
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

</script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection