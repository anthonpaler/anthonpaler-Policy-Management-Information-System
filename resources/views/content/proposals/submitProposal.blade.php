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
    <a href="{{route(    getUserRole().'.meetings')}}">Meetings</a>
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
                            <h4 class="">{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }} {{ config("meetings.council_types." . ['local_level', 'university_level', 'board_level'][$meeting->getMeetingCouncilType()] . ".{$meeting->council_type}") }}
                            {{$meeting->year}}</h4>
                            <div class="">
                                <span class="btn btn-sm btn-{{$meeting->status == 0 ? 'primary' : "danger" }} d-flex gap-1">
                                    {!! $meeting->status == 0 ? "<i class='bx bxs-lock-open-alt' ></i>" : "<i class='bx bxs-lock-alt' ></i>" !!}
                                    {{ config('meetings.status.'.$meeting->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between  meeting-sub-details">
                            <div class="d-flex flex-wrap gap-3 mb-1">
                                <span class="form-label">Submission: </span>
                                <h6>{{ \Carbon\Carbon::parse($meeting->submission_start)->format('F d, Y, h:i A') }} - {{ \Carbon\Carbon::parse($meeting->submission_end)->format('F d, Y, h:i A') }}</h6>
                            </div>
                            <div class="d-flex flex-wrap gap-3 mb-1">
                                <span class="form-label">Meeting Date: </span>
                                <h6>{{ $meeting->meeting_date_time ? \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, h:i A') : 'Not yet set' }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="p-3">

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form method="POST" action="{{ route(getUserRole().'.proposals.store', ['meeting_id' => encrypt($meeting->id)]) }}" enctype="multipart/form-data" id="proposalFrm">
        <div class="">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 mb-3">
                <span class="text-muted m-0">Please fill all the required fields <em class="text-danger">*</em></span>
            </div>
            <div class="row ">
                <!-- Proposal details -->
                <div class="col-md-6 mb-4">
                    <div class="card" style="height:100%;">
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
                                        @switch(auth()->user()->role)
                                            @case(0)
                                                <option value="1" >{{ config('proposals.matters.1') }}</option>
                                                @break
                                            @case(1)
                                                <option value="2" >{{ config('proposals.matters.2') }}</option>
                                                @break
                                            @default
                                            @foreach (config('proposals.matters') as $key => $value)
                                                <option value="{{ $key }}" >
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        @endswitch
                                    </select>
                                </div>
                                @error('matter')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                            </div>
                                
                            <div class="mb-4">
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
                                        @switch(auth()->user()->role)
                                            @case(0)
                                                <option value="1" >{{ config('proposals.requested_action.1') }}</option>
                                                <option value="3" >{{ config('proposals.requested_action.3') }}</option>
                                                @break
                                            @case(1)
                                                <option value="2" >{{ config('proposals.requested_action.2') }}</option>
                                                <option value="3" >{{ config('proposals.requested_action.3') }}</option>
                                                @break
                                            @default
                                            @foreach (config('proposals.requested_action') as $key => $value)
                                                <option value="{{ $key }}" >
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        @endswitch
                                    </select>
                                </div>
                                @error('action')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3" id="subTypeContainer" style="{{auth()->user()->role == 1 ? 'display: block;' : 'display: none;'}}">
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
                <div class="col-md-6 mb-4">
                    <div class="card" style="height:100%;">
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
                                        <li data-id="{{auth()->user()->employee_id;}}" data-name="{{auth()->user()->name;}}" data-email="{{auth()->user()->email;}}" data-image="{{auth()->user()->image;}}" id="primaryProponent">
                                            <div class="d-flex justify-content-between align-items-center ms-2 me-2 flex-wrap gap-2">
                                                <div class="d-flex justify-content-start align-items-center ">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3">
                                                            <img src="{{auth()->user()->image;}}" alt="Avatar" class="rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <a href="" class="text-heading text-truncate m-0">
                                                            <span class="fw-medium">{{auth()->user()->name;}} (You)</span>
                                                        </a>
                                                        <small>{{auth()->user()->email;}}</small>
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
                        <div class="upload-container mb-3">
                            <label class="form-label" for="fileUpload">Proposal File/s <span class="ms-1 text-danger">*</span></label>
                            <div id="dropArea" class="drop-area">
                                <span class="upload-text">Drag & Drop files here, or <strong class="text-primary">click to upload</strong></span>
                                <small class="text-muted">Accepted formats: .pdf, .xls, .xlsx, and .csv only</small>
                                <input type="file" id="fileUpload" accept=".pdf,.xls,.xlsx,.csv" multiple hidden>
                            </div>
                            <h5 id="uploadedFilesLabel" class="file-header mt-3"><i class='bx bx-file'></i> Uploaded Files</h5>
                            <ul id="fileList" class="file-list mt-3">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="mt-4 btn btn-primary d-flex gap-2" id="submitProposalBtn">
                <i class='bx bx-send' ></i>
                <span class="text-nowrap">Submit Proposal</span> 
            </button>
        </div>
    </form>
</div>

<script>
    function getImageByFileType(fileType) {
        switch (fileType) {
            case "pdf":
                return "{{ asset('assets/img/icons/file-icons/pdf.png') }}";
            case "xls":
                return "{{ asset('assets/img/icons/file-icons/xls.png') }}";
            case "xlsx":
                return "{{ asset('assets/img/icons/file-icons/xlsx.png') }}";
            case "csv":
                return "{{ asset('assets/img/icons/file-icons/csv-file.png') }}";
            default:
                return "{{ asset('assets/img/icons/file-icons/file.png') }}";
        }
    }
</script>
<script src="{{asset('assets/js/customFileUplaod.js')}}"></script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection