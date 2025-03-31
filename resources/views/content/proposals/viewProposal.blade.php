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
                            <span class="badge bg-label-{{ in_array($proposal->status, [2,5,6,7]) ? 'danger' : 'primary'}}">{{ config('proposals.status.'.$proposal->status) }}</span>
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
                                rows="3"
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
                                                            <img class="rounded-circle avatar-sm" src="{{ $proponent->image && trim($proponent->image) !== '' ? $proponent->image : asset('assets/img/avatars/default-avatar.jpg') }}
                                                            " alt="Avatar">
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
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="sub_type">Type of Matter or Proposal <span class="ms-1 text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <span id="matters-icon" class="input-group-text"><i class="bx bx-briefcase"></i></span>
                                        <select
                                            class="form-select @error('matter') is-invalid @enderror"
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
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="action">Requested Action <span class="ms-1 text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <span id="action-icon" class="input-group-text"><i class="bx bx-task"></i></span>
                                        <select
                                            class="form-select @error('action') is-invalid @enderror"
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
                            </div>
                        </div>
                        <div class="mb-3" id="subTypeContainer" style="{{$proposal->type == 2 ? 'display: block;' : 'display: none;'}}">
                            <label class="form-label" for="sub_type">Sub Type</label>
                            <div class="input-group input-group-merge">
                                <span id="sub-type-icon" class="input-group-text"><i class="bx bx-category-alt"></i></span>
                                <select
                                    name="sub_type"
                                    id="sub_type"
                                    class="form-select @error('sub_type') is-invalid @enderror"
                                    aria-label="Sub-type of proposal"
                                    aria-describedby="sub-type-icon"
                                    required
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
                                <table id="proposalFilesTable" class="table table-striped sortable">
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

                                                    // DYNAMIC FILE ICON
                                                    $fileExtension = pathinfo($file->file, PATHINFO_EXTENSION);
                                                                $iconPath = asset('assets/img/icons/file-icons/' . ($fileExtension === 'pdf' ? 'pdf.png' : 
                                                                            ($fileExtension === 'xls' ? 'xls.png' : 
                                                                            ($fileExtension === 'xlsx' ? 'xlsx.png' : 
                                                                            ($fileExtension === 'csv' ? 'csv-file.png' : 'file.png')))));
                                                    // FILE STATUS COLOR
                                                    $statusColors = [ 'secondary','warning', 'success', 'danger', 'primary']; 
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex gap-3">
                                                            <input type="checkbox" class="form-check-input select-proposal-file d-none" data-id="{{ $file->id }}"> 
                                                            <span class="text-muted file_order_no">
                                                                {{  $file->order_no }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="">
                                                                <img src="{{ $iconPath }}" class="file-icon" alt="File Icon">
                                                            </div>
                                                            <div class="file-name">
                                                                <span style="color: #3E4043;" class="text-wrap view-single-file-preview" 
                                                                data-file-url="/storage/proposals/{{$file->file}}">{{$file->file}}</span>
                                                                <div class="d-flex gap-2">
                                                                    <small class="text-muted version">Version  {{ $file->version }}</small>
                                                                    <small class="text-{{ $statusColors[$file->file_status] }}">{{ config(key: 'proposals.proposal_file_status.'.$file->file_status) }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <button 
                                                                class="action-btn success rename-file-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#renameFileModal"
                                                                data-id="{{ $file->id }}"
                                                                data-filename="{{ $file->file }}">
                                                                <i class='bx bx-rename'></i>
                                                                <span class="tooltiptext">Rename</span>
                                                            </button>
                                                            
                                                            <button class="action-btn danger delete-proposal-file"  data-id="{{ $file->id }}" >
                                                                <i class='bx bx-trash-alt'></i>
                                                                <span class="tooltiptext">Delete</span>
                                                            </button>
                                                            
                                                            <button class="action-btn primary resubmit-proposal" data-id="{{ $file->id }}" >
                                                                <i class='bx bx-upload' ></i>
                                                                <span class="tooltiptext">Reupload</span>
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
                                // DYNAMIC FILE ICON
                                $fileExtension = pathinfo($file->file, PATHINFO_EXTENSION);
                                $iconPath = asset('assets/img/icons/file-icons/' . 
                                    ($fileExtension === 'pdf' ? 'pdf.png' : 
                                    ($fileExtension === 'xls' ? 'xls.png' : 
                                    ($fileExtension === 'xlsx' ? 'xlsx.png' : 
                                    ($fileExtension === 'csv' ? 'csv-file.png' : 'file.png')))));
                            @endphp
                            <div class="table-responsive text-nowrap old-version-files d-none files-table">
                                <table id="" class="table table-striped ">
                                    <thead>
                                        <tr>
                                            <th style="">File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proposal->files as $file)
                                            @if ($file->is_active == 0)
                                                @php
                                                    $countOldVersion++;
                                                    // DYNAMIC FILE ICON
                                                    $fileExtension = pathinfo($file->file, PATHINFO_EXTENSION);
                                                    $iconPath = asset('assets/img/icons/file-icons/' . 
                                                        ($fileExtension === 'pdf' ? 'pdf.png' : 
                                                        ($fileExtension === 'xls' ? 'xls.png' : 
                                                        ($fileExtension === 'xlsx' ? 'xlsx.png' : 
                                                        ($fileExtension === 'csv' ? 'csv-file.png' : 'file.png')))));
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="">
                                                                <img src="{{ $iconPath }}" class="file-icon" alt="File Icon">
                                                            </div>
                                                            <div class="file-name">
                                                                <span style="color: #3E4043;" class="text-wrap view-single-file-preview" 
                                                                data-file-url="/storage/proposals/{{$file->file}}">{{$file->file}}</span>
                                                                <div class="d-flex gap-2">
                                                                    <small class="text-muted version">Version  {{ $file->version }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        @if ($countOldVersion == 0)
                                            <td colspan="1">
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
                                        <div class="{{$log->user->employee_id == session('employee_id') ? 'sender' : 'reciever' }}">
                                            <div class="d-flex gap-4 justify-content-between {{ $log->user->employee_id == session('employee_id') ? 'flex-row-reverse' : '' }}">
                                                <div class="d-flex justify-content-start align-items-center ">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3">
                                                            <img src="{{ asset($log->user->image ?? 'assets/img/avatars/default.png') }}" alt="Avatar" class="rounded-circle">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <a class="text-heading text-truncate m-0">
                                                            <span class="fw-medium">{{$log->user->name}}</span>
                                                        </a>
                                                        <small class="text-wrap">{{ config('usersetting.role.'.$log->user->role) }}</small>
                                                    </div>
                                                </div>
                                               
                                                <div class="d-flex flex-column">
                                                    <small class="text-{{ in_array($log->status, [2,5, 6,7]) ? 'danger' : 'primary'}} {{ $log->user->employee_id == session('employee_id') ? 'align-self-start' : 'align-self-end' }}">{{ config('proposals.status.'.$log->status) }}</small>
                                                    <small class="text-muted {{ $log->user->employee_id == session('employee_id') ? 'align-self-start' : 'align-self-end' }}">{{ $log->created_at->format('F d, Y') }}</small>
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
                            <div class="d-flex gap-3 w-100 flex-wrap justify-content-end">
                                <div class="btn-group">
                                    <button type="button" class="form-control dropdown-toggle d-flex gap-2 justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false" data-id="" id="proposalStatusInput" >Proposal Actions</button>

                                    @php
                                        $currentDateTime = now();
                                        $meetingDateTime = \Carbon\Carbon::parse($proposal->meeting->meeting_date_time);
                                    @endphp

                                    <ul class="dropdown-menu">
                                        @foreach (array_slice(config('proposals.proposal_action'), 0, 7, true) as $index => $item)
                                            @php
                                               
                                                if(session('user_role') == 5 ){
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
                                                        $isDisabled = in_array($index, [2, 3, 4, 5, 6, 9]); // Enable 2-6 if current date is after or equal to meeting date
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

                                            @if (in_array($index, [1, 5, 9])) 
                                                <li><hr class="dropdown-divider"></li>
                                            @endif
                                        @endforeach
                                        
                                        
                                        @if($proposal->type == 3)
                                            <li>
                                                <span class="dropdown-item proposal-action {{ $isDisabled ? 'disabled' : '' }}" 
                                                    data-id="9" 
                                                    data-label="Confirm">
                                                    Confirm
                                                </span>
                                            </li>
                                        @endif
                                    </ul>

                                </div>
                                <!-- <div class="flex-grow-1">
                                    <input type="text" class="form-control flex-grow-1" data-id="" value="Select Action" id="proposalStatusInput"  disabled>
                                </div> -->
                                <button class="btn btn-{{ $proposal->meeting->status == 1 ? 'danger': 'primary' }} d-flex align-items-center gap-2 text-nowrap" 
                                        id="updateProposalStatus" 
                                        data-id="{{ encrypt($proposal->id) }}" 
                                        {{ $proposal->meeting->status == 1 ? 'disabled' : '' }}>
                                    
                                    {!! $proposal->meeting->status == 1 ? "<i class='bx bxs-lock-alt'></i>" : "<i class='bx bx-send'></i>" !!}
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
    <div class="modal-dialog modal-xl" style="height: 95%; display: flex; align-items: center;">
        <div class="modal-content" style="height: 100%;">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title" id="fileModalLabel">File Preview</h5>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bx bx-fullscreen full-screen-file-preview" id="toggleIframeFullscreen"></i>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="flex-grow: 1; overflow: hidden;">
                <iframe id="fileIframe" src="" width="100%" height="100%" style="height: 100%;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Renaming File -->
<div class="modal fade" id="renameFileModal" tabindex="-1" aria-labelledby="renameFileModallLabel" 
     aria-hidden="true" data-file-id="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameFileModallLabel">Rename File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="">Current File Name</label>
                    <div class="input-group input-group-merge">
                        <span id="" class="input-group-text">
                            <i class='bx bx-file' ></i>
                        </span>
                        <input type="text" class="form-control" id="currentFileName" value="" disabled>
                    </div>
                </div>
                <div class="">
                    <label class="form-label" for="">New File Name</label>
                    <div class="input-group input-group-merge">
                        <span id="" class="input-group-text">
                            <i class='bx bx-rename' ></i>
                        </span>
                        <input type="text" class="form-control" id="newFileName" placeholder="Enter new file name">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary d-flex gap-2" id="renameFileBtn">Rename</button>
            </div>
        </div>
    </div>
</div>
<script>
    var proposalStatus = @json(config('proposals.status'));
    $('#matter').on('change', function() {
        var matter = $(this).val();
        var subType = $('#sub_type');
        var actionSelect = $('#action');

        actionSelect.empty();

        if (matter == 1) {
            actionSelect.append(`
                @if (session('user_role') == 3)
                    <option value="4">Endorsement for Local ACAD</option>
                    <option value="1">Endorsement for UACAD</option>
                @endif
                @if (session('user_role') == 4)
                    <option value="6">Approval for UACAD</option>
                    <option value="3">Endorsement for BOR</option>
                @endif
                @if (session('user_role') == 5)
                    <option value="8">BOR Approval</option>
                @endif
            `);
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');
        } else if (matter == 2) {
            subType.prop('disabled', false);
            $('#subTypeContainer').css('display', 'block');

            actionSelect.append(`
                @if (session('user_role') == 3)
                    <option value="5">Endorsement for Local ADCO</option>
                    <option value="2">Endorsement for UADCO</option>
                @endif
                @if (session('user_role') == 4)
                    <option value="7">Approval for UADCO</option>
                    <option value="3">Endorsement for BOR</option>
                @endif
                @if (session('user_role') == 5)
                    <option value="8">BOR Approval</option>
                @endif
            `);
        }else if (matter == 3) {
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');

            actionSelect.append(`
                <option value="3">Endorsement for BOR</option>
                <option value="9">BOR Confirmation</option>
            `);
        }
        else if (matter == 4) {
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');

            actionSelect.append(`
                <option value="3">Endorsement for BOR</option>
                <option value="10">BOR Information</option>
            `);
        }
    });

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