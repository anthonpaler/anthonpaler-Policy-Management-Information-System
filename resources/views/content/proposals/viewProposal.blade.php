@extends('layouts/contentNavbarLayout')

@section('title', 'Proposal Details')

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
        <a href="{{route(    getUserRole().'.proposals')}}">Meetings Proposals</a>
        <i class='bx bx-chevron-right' ></i>
        <a href="#">Proposals</a>
        <i class='bx bx-chevron-right' ></i>
        <a href="#">Proposal</a>
    </div>
</div>

<div class="p-0">
    <div class="row">
        <div class="col col-lg-5 mb-4">
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
                        <!-- <div class="col-md-6 c-field-p w-100">
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
                        </div> -->
                        <div class="mb-3" id="" style="">
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
                                                <!-- @if($proponent->employee_id === session('employee_id'))
                                                    <div class="">
                                                        <small class="badge bg-label-secondary d-flex align-items-center gap-2">
                                                            <i class='bx bx-user-check'></i>Submitter
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="">
                                                        <small class="badge bg-label-danger d-flex align-items-center gap-2 remove" data-id="{{$proponent->employee_id}}"><i class='bx bx-trash'></i>Remove</small>
                                                    </div> 
                                                @endif -->
                                            </div>
                                        </li>
                                    @endforeach
<!-- 
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
                                    </script> -->
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
                            <div class="d-flex justify-content-between align-items-center custom_tab_wrapper mb-3">
                                <div class="">
                                    <ul class="custom_tab_list">
                                        <li class="custom_tab_item file-tab latest-file-tab active" data-status = "0">
                                            <div class="">
                                                <i class='bx bx-file' ></i>
                                                <span>Latest File Versions</span>
                                            </div>
                                        </li>
                                        <li class="custom_tab_item file-tab old-file-tab" data-status = "1">
                                            <div class="">
                                                <i class='bx bxs-file-blank' ></i>
                                                <span>Old File Versions</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <input type="file" id="file-upload" style="display: none;" accept=".pdf, .xls, .xlsx, .csv"
                            >
                            @php
                                $countLatestVersion = 0;
                            @endphp
                            <div class="table-responsive text-nowrap latest-version-files">
                                <table id="proposalFilesTable" class="table table-bordered sortable">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;"></th>
                                            <th style="">File</th>
                                            <th style="width: 100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proposal->files as $file)
                                            @if ($file->is_active == 1)
                                                @php
                                                    $countLatestVersion++;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex gap-3">
                                                            <input type="checkbox" class="form-check-input select-proposal-file" data-id="{{ $file->id }}" > 
                                                            <span class="text-muted file_order_no">
                                                                {{  $file->order_no }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-3">
                                                            <div class="proposal-file-img">
                                                                <img src="{{ asset('assets/img/icons/document/folder_3.png') }}" alt="">
                                                            </div>
                                                            <div class="d-flex flex-column gap-2">
                                                                <span class="text-wrap"  data-bs-toggle="modal" 
                                                                data-bs-target="#fileModal"
                                                                data-file-url="/storage/proposals/{{$file->file}}">{{ $file->file }} </span>
                                                                <div class="d-flex gap-2">
                                                                    <span class="badge bg-label-primary">{{ config(key: 'proposals.proposal_file_status.'.$file->file_status) }}</span>
                                                                    <span class="badge bg-label-success">Version {{ $file->version }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <button class="btn btn-primary btn-sm rename-file-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#renameFileModal"
                                                                data-id="{{ $file->id }}"
                                                                data-filename="{{ $file->file }}">
                                                                <i class='bx bx-rename'></i>
                                                            </button>

                                                            <!-- <button class="btn btn-sm btn-success resubmit-proposal"
                                                            
                                                            data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-original-title="Resubmit File "

                                                            data-id="{{ $file->id }}" {{ (!in_array($proposal->status, [2,5,6]) && $proposal->is_edit_disabled && $file->file_status) ? 'disabled' : '' }}>
                                                                <i class='bx bx-upload' ></i>
                                                            </button> -->

                                                            <button type="button" class="btn btn-sm  btn-danger delete-proposal-file" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-original-title="Delete File "
                                                            data-id="{{ $file->id }}" {{ (!in_array($proposal->status, [2,5,6]) && $proposal->is_edit_disabled  && $file->file_status) ? 'disabled' : '' }}
                                                            > 
                                                                <i class='bx bxs-trash'></i>
                                                            </button>
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        @if ($countLatestVersion == 0)
                                            <td colspan="3">
                                                <div
                                                    class="alert alert-info"
                                                    role="alert"
                                                >
                                                    <p>No latest version files</p>
                                                </div>
                                            </td>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @php
                                $countOldVersion = 0;
                            @endphp
                            <div class="table-responsive text-nowrap old-version-files d-none">
                                <table id="" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;"></th>
                                            <th style="">File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proposal->files as $file)
                                            @if ($file->is_active == 0)
                                                @php
                                                    $countOldVersion++;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex gap-3">
                                                            <!-- <input type="checkbox" class="form-check-input select-proposal-file" data-id="{{ $file->id }}" > 
                                                            <span class="text-muted file_order_no">
                                                                {{  $file->order_no }}
                                                            </span> -->
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-3">
                                                            <div class="proposal-file-img">
                                                                <img src="{{ asset('assets/img/icons/document/folder_3.png') }}" alt="">
                                                            </div>
                                                            <div class="d-flex flex-column gap-2">
                                                                <span class="text-wrap"  data-bs-toggle="modal" 
                                                                data-bs-target="#fileModal"
                                                                data-file-url="/storage/proposals/{{$file->file}}">{{ $file->file }} </span>
                                                                <div class="d-flex gap-2">
                                                                    <span class="badge bg-label-success">Version {{ $file->version }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        @if ($countOldVersion == 0)
                                            <td colspan="2">
                                                <div
                                                    class="alert alert-info"
                                                    role="alert"
                                                >
                                                    <p>No old version files</p>
                                                </div>
                                            </td>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <!-- <small class="text-muted text-wrap d-flex gap-2"><strong>Note:</strong><em>Please be cautious when reuploading and deleting a proposal file.</em></small> -->
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <label class="form-label" for="file">Add Attachment/s (Optional)</label>
                                <div id="dropArea" class="drop-area">
                                    <span class="upload-text">Drag & Drop files here, or <strong class="text-primary">click to upload</strong></span>
                                    <small class="text-muted">Accepted formats: .pdf, .xls, .xlsx, and .csv only</small>
                                    <input type="file" id="fileUpload" accept=".pdf,.xls,.xlsx,.csv" multiple hidden>
                                </div>

                                <div class="file-header mt-3" id="uploadedFilesLabel">
                                    <label class="form-label d-flex align-items-center " for="">
                                        <i class='bx bx-file'></i> UPLOADED FILES
                                    </label>
                                </div>

                                <ul id="fileList" class="file-list mt-3">
                                </ul>
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
        <div class="col mb-4" sty>
            <div class="card mb-4 proposal-action-wrapper">
                <div class="card-header com-sec-header ">
                    <h6>PROPOSAL COMMENTS / SUGGESTIONS</h6>
                </div>
                <div class="comment-area">
                    <div class="">
                        @if($proposal_logs->isNotEmpty())
                            @php $noLogsFound = true; @endphp
                            @foreach($proposal_logs as $log)
                                @if (in_array($log->action, [1,4,5,6]))
                                    @php $noLogsFound = false; @endphp
                                    <div class="com-wrapper d-flex {{ $log->user->employee_id == session('employee_id') ? 'justify-content-end' : 'justify-content-start' }}">
                                        <div class="{{$log->user->employee_id == session('employee_id') ? 'sender' : 'receiver' }}">
                                            <div class="d-flex gap-4 justify-content-between">
                                                <div class="d-flex gap-2">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar avatar-sm">
                                                            <img src="{{ asset($log->user->image ?? 'assets/img/avatars/default.png') }}" 
                                                                alt="{{ $log->user->name }}" class="w-px-40 h-auto rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 user-info">
                                                        <span class="fw-medium d-block">{{ $log->user->name }}</span>
                                                        <small class="text-muted">{{ config('usersetting.role.'.$log->user->role) }}</small>
                                                    </div>
                                                </div>
                                               
                                                <div class="d-flex flex-column">
                                                    <span class="badge bg-label-warning" style="text-transform: none;">{{ config('proposals.proposal_action.'.$log->action) }}</span>
                                                    <small class="text-muted align-self-end">{{ $log->created_at->format('F d, Y') }}</small>
                                                </div>
                                            </div>
                                            <div class="card p-3 mt-2">
                                                <p>{{ $log->comments ?: 'No comments provided.' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @if($noLogsFound)
                                <div class="alert alert-info mt-3" role="alert">
                                    <i class="bx bx-info-circle"></i> There is no comments or suggestions yet.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info mt-3" role="alert">
                                <i class="bx bx-info-circle"></i> There is no comments or suggestions yet.
                            </div>
                        @endif

                    </div>
                </div>
            
                <div class="p-4 porposal-action-con">
                    <div class="">
                        <!-- <small class="text-light fw-medium">Comment</small> -->
                        <div class="mb-3">
                            <textarea name="comment" class="form-control" id="comment" required placeholder="Add comment..." rows="3" disabled></textarea>
                        </div>
                        <div class="d-flex justify-content-between gap-3">
                            <div class="d-flex gap-3 w-100">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary text-nowrap">Proposal Action</button>
                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>

                                    @php
                                        $currentDateTime = now();
                                        $meetingDateTime = \Carbon\Carbon::parse($proposal->meeting->meeting_date_time);
                                    @endphp

                                    <ul class="dropdown-menu">
                                        @foreach (array_slice(config('proposals.proposal_action'), 0, 7, true) as $index => $item)
                                            @php
                                               
                                                if(auth()->user()->role == 5 ){
                                                    if (in_array($index, [3,5])) {
                                                        continue;
                                                    }
                                                }

                                                $isDisabled = true; 
                                                
                                                if ($meetingDateTime) {
                                                    if ($currentDateTime->greaterThan($meetingDateTime)) {
                                                        $isDisabled = in_array($index, [0, 1]); // Enable 0 & 1 if current date is before meeting date
                                                        if($proposal->status == 1){
                                                            $isDisabled = in_array($index, [0]);
                                                        }
                                                    }
                                                    else {
                                                        $isDisabled = in_array($index, [2, 3, 4, 5, 6]); // Enable 2-6 if current date is after or equal to meeting date
                                                    }
                                                }
                                            @endphp
                                            
                                            <li>
                                                <span class="dropdown-item proposal-action {{ $isDisabled ? 'disabled' : '' }}" 
                                                    data-id="{{ $index }}" 
                                                    data-label="{{ $item }}">
                                                    {{ $item }}
                                                </span>
                                            </li>

                                            @if (in_array($index, [1, 5])) 
                                                <li><hr class="dropdown-divider"></li>
                                            @endif
                                        @endforeach
                                    </ul>

                                </div>
                                <div class=" flex-grow-1">
                                    <input type="text" class="form-control flex-grow-1" data-id="" value="Select Action" id="proposalStatusInput"  disabled>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-{{ $proposal->meeting->status == 1 ? 'danger': 'primary' }} d-flex gap-2 text-nowrap" 
                                        id="updateProposalStatus" 
                                        data-id="{{ encrypt($proposal->id) }}" 
                                        {{ $proposal->meeting->status == 1 ? 'disabled' : '' }}>
                                    
                                    {!! $proposal->meeting->status == 1 ? "<i class='bx bxs-lock-alt'></i>" : "<i class='bx bxs-send'></i>" !!}
                                    Update Proposal Status
                                </button>
                            </div>
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
<!-- Modal for Renaming File -->
<div class="modal fade" id="renameFileModal" tabindex="-1" aria-labelledby="renameFileModallLabel" 
     aria-hidden="true" data-file-id="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameFileModallLabel">Rename File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="">Current File Name</label>
                        <div class="input-group input-group-merge">
                            <span id="" class="input-group-text">
                                <i class='bx bx-file' ></i>
                            </span>
                            <input type="text" class="form-control" id="currentFileName" value="" disabled>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="">New File Name</label>
                        <div class="input-group input-group-merge">
                            <span id="" class="input-group-text">
                                <i class='bx bx-rename' ></i>
                            </span>
                            <input type="text" class="form-control" id="newFileName" placeholder="Enter new file name">
                            <button class="btn btn-primary d-flex gap-2" id="renameFileBtn">
                                Rename
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var proposalStatus = @json(config('proposals.status'));

    let deletedFiles = [];
    let reuploadedFiles = [];

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

    // COSTUM MULTIPLE FILE UPLOAD
 
    const dropArea = document.getElementById("dropArea");
    const fileUpload = document.getElementById("fileUpload");
    const fileList = document.getElementById("fileList");
    let uploadedProposalFiles = [];

    dropArea.addEventListener("click", () => fileUpload.click());
    dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.style.background = "#f1f1f1";
    });
    dropArea.addEventListener("dragleave", () => {
        dropArea.style.background = "#fff";
    });
    dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.style.background = "#fff";
        handleFiles(e.dataTransfer.files);
    });
    fileUpload.addEventListener("change", (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach((file) => {
            if (!uploadedProposalFiles.some(f => f.name === file.name)) {
                uploadedProposalFiles.push(file);
                displayFile(file);
                simulateUpload(file);
            }
        });
    }

    function displayFile(file) {
        const listItem = document.createElement("li");
        listItem.classList.add("file-item");    

        const uploadedFilesLabel = document.getElementById("uploadedFilesLabel");

        if (fileList.children.length === 0) {
            uploadedFilesLabel.style.display = "block";
        }

        const fileType = file.name.split('.').pop().toLowerCase();
        const iconSrc = getImageByFileType(fileType); 

        listItem.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <div class="">
                    <img src="${iconSrc}" class="file-icon" alt="File Icon">
                </div>
                <div class="file-name">
                    <strong>${file.name}</strong>
                    <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="delete-file-btn"><i class='bx bx-trash'></i></button>
                <div class="progress-circle" data-progress="0">
                    <span class="progress-text">0%</span>
                </div>
            </div>
        `;

        console.log(uploadedProposalFiles);

        listItem.querySelector(".delete-file-btn").addEventListener("click", () => {
            uploadedProposalFiles = uploadedProposalFiles.filter(f => f.name !== file.name);
            listItem.remove();

            if (fileList.children.length === 0) {
                uploadedFilesLabel.style.display = "none";
            }
            console.log(uploadedProposalFiles);
        });

        fileList.appendChild(listItem);
    }

    // CUSTOM UPLOAD CIRCLE PROGRESS BAR
    function simulateUpload(file) {
        const listItem = Array.from(fileList.children).find(li => li.querySelector("strong").textContent === file.name);
        if (!listItem) return;

        const progressCircle = listItem.querySelector(".progress-circle");
        const progressText = listItem.querySelector(".progress-text");
        let progress = 0;
        const interval = setInterval(() => {
            if (progress >= 100) {
                clearInterval(interval);
                progressText.innerHTML  =`<i class='bx bx-check progress-check'></i>`;
                progressCircle.style.background = "#39DA8A";
                return;
            }
            progress += 10;
            progressCircle.setAttribute("data-progress", progress);
            progressCircle.style.background = `conic-gradient(#fd7e14 ${progress}%, #ffffff ${progress}% 100%)`;
            progressText.textContent = `${progress}%`;
        }, 200);
    }

    $(".resubmit-proposal").on('click', function (e) {
        e.preventDefault();
        
        var file_id = $(this).data('id');
        $("#file-upload").data("id", file_id).click(); // Store file ID and trigger file input
        $(this).closest(".file-status").html("Revised");
    });
    
    $("#file-upload").on("change", function (e) {
        var file = e.target.files[0]; // Get selected file
        var file_id = $(this).data("id");

        if (file) {
            reuploadedFiles.push({ file_id, file });
        }

        console.log(reuploadedFiles);

        if (this.files.length > 0) {
            var proposalRow = $("button.resubmit-proposal[data-id='" + file_id + "']").closest("tr");

            // Update the status to "Revised"
            proposalRow.find(".badge.bg-label-primary").text("Revised");

            // Increment version number
            var versionElement = proposalRow.find(".badge.bg-label-success");
            var currentVersion = parseInt(versionElement.text().replace("Version ", "").trim());
            var newVersion = currentVersion + 1;

            // Update version badge
            versionElement.text("Version " + newVersion);

            // Update file name
            proposalRow.find(".text-wrap").text(file.name);
        }
    });

    
    $(".delete-proposal-file").on("click", function (e) {
        e.preventDefault();
        var file_id = $(this).data("id");
        var button = $(this); 

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                deletedFiles.push(file_id);
                button.closest("tr").remove();
                console.log(deletedFiles);
            }
        });
    });


    $("#updateProposal").on('click', function (e) {
        e.preventDefault();

        var proposalFrm = $("#editProposalFrm")[0]; // Get the raw form element
        var formData = new FormData(proposalFrm); // Create FormData from the form

        var actionUrl = $("#editProposalFrm").attr('action');


        uploadedProposalFiles.forEach((file, index) => {
            formData.append(`proposal_files[${index}]`, file);
        });

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

<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection