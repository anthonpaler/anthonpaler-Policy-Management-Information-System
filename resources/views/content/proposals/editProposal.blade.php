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
@php
    // Define proposal status classes dynamically
    $statusClass = match ($proposal->status) {
        2, 7 => 'danger',
        5, 6 => 'warning',
        1, 8, 9 => 'primary',
        3, 10 => 'success',
        4 => 'info',
        default => 'secondary'
    };
@endphp
<div class="p-0">
    <div class="row">
        <div class="col col-lg-5 mb-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route(getUserRole().'.proposal.edit.save', ['proposal_id' => encrypt($proposal->id)]) }}" method="post" id="editProposalFrm">
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <h6 class="m-0">PROPOSAL DETAILS </h6>                       
                            <span class="badge bg-label-{{ $statusClass}}">{{ config('proposals.status.'.$proposal->status) }}</span>
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
                            <div class="mb-3" id="" style="">
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
                        <div class="mb-3" id="" style="">
                            <label class="form-label" for="proponents">Proponent/s <span class="ms-1 text-danger">*</span></label>
                            <div class="form-control proponent-con" style="">
                                <input type="text" class="form-control mb-3" value="{{$proposal->employee_id}}" id="proponents" name="proponents" hidden>
                                <ul class="" id="proponentListCon">
                                    @foreach ($proposal->proponents as $proponent)
                                        <li data-id="{{$proponent->employee_id}}" data-name="{{$proponent->name}}" data-email="{{$proponent->email}}" data-image="{{$proponent->image}}" id="primaryProponent">
                                            <div class="d-flex justify-content-between align-items-center ms-2 me-2 flex-wrap gap-2">
                                                <div class="d-flex justify-content-start align-items-center ">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3">
                                                            <img src="{{$proponent->image}}" alt="Avatar" class="rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <a class="text-heading text-truncate m-0">
                                                            <span class="fw-medium">{{$proponent->name}}</span>
                                                        </a>
                                                        <small class="text-wrap">{{$proponent->email}}</small>
                                                    </div>
                                                </div>
                                                @if($proponent->employee_id === session('employee_id'))
                                                    <div class="">
                                                        <small class="badge bg-label-secondary d-flex align-items-center gap-2">
                                                            <i class='bx bx-user-check'></i>( Me )
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
                                        $selectedProponents = $proposal->proponents->map(function ($proponent) {
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
                            <div class="table-responsive text-nowrap latest-version-files files-table">
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
                                                            <!-- <input type="checkbox" class="form-check-input select-proposal-file" data-id="{{ $file->id }}" >  -->
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
                                                                <span class="text-wrap view-file-preview"   data-bs-toggle="modal" 
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

                                                            <button class="btn btn-sm btn-success resubmit-proposal"
                                                            
                                                            data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-original-title="Resubmit File "

                                                            data-id="{{ $file->id }}" {{ (!in_array($proposal->status, [2,5,6]) && $proposal->is_edit_disabled && $file->file_status) ? 'disabled' : '' }}>
                                                                <i class='bx bx-upload' ></i>
                                                            </button>

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
                            <div class="table-responsive text-nowrap old-version-files d-none files-table">
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
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-3">
                                                            <div class="proposal-file-img">
                                                                <img src="{{ asset('assets/img/icons/document/folder_3.png') }}" alt="">
                                                            </div>
                                                            <div class="d-flex flex-column gap-2">
                                                                <span class="text-wrap view-file-preview"   data-bs-toggle="modal" 
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
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <label class="form-label" for="file">Add Attachment/s (Optional)</label>
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
                                                    <div class="alert alert-{{ $logStatusClass }}" role="alert">
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
</script>
<script src="{{asset('assets/js/customFileUplaod.js')}}"></script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection