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
        <div class="col col-lg-5 mb-4" >
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route(getUserRole().'.proposal.edit', ['proposal_id' => encrypt($proposal->id)]) }}" method="post" id="editProposalFrm">
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
                                rows="3"
                            >{{ $proposal->title }}</textarea>
                            @error('title')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="form-label" for="">Proponent <span class="ms-1 text-danger">*</span></label>
                        <div class="border rounded p-2 mb-3">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($proposal->proponentsList as $proponent)
                                    <div class="border rounded p-2 flex-grow-1">
                                        <div class="d-flex  ">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-sm">
                                                    <img src="{{ $proponent->image ?? '/default-avatar.png' }}" alt class="w-px-40 h-auto rounded-circle">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 user-info">
                                                <span class="fw-medium d-block">{{ $proponent->name }}</span>
                                                <small class="text-muted">{{ config('usersetting.role.'.$proponent->role) }}</small>
                                            </div>
                                        </div>
                                </div>
                                @endforeach
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
                                    <option value="">Select Type of Matter or Proposal</option>

                                    @if (in_array(auth()->user()->role, [3,4,5]))
                                            <option value="1" {{ $proposal->type === 1 ? 'selected' : ''}}>Academic Matters</option>
                                            <option value="2" {{ $proposal->type === 2 ? 'selected' : ''}}>Administrative Matters</option>
                                    @elseif(auth()->user()->role == 5)
                                        <option value="3" {{ $proposal->type === 3 ? 'selected' : ''}}>Matters for Confirmation</option>
                                        <option value="4" {{ $proposal->type === 4 ? 'selected' : ''}}>Matters for Information</option>
                                    @endif

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
                                <option value="">Select an action</option>
                                    @if (auth()->user()->role == 3)
                                        <option value="4" {{ $proposal->action === 4 ? 'selected' : '' }}>Endorsement for Local ACAD</option>
                                        <option value="5" {{ $proposal->action === 5 ? 'selected' : '' }}>Endorsement for Local ADCO</option>
                                        <option value="1" {{ $proposal->action === 1 ? 'selected' : '' }}>Endorsement for UACAD</option>
                                        <option value="2" {{ $proposal->action === 2 ? 'selected' : '' }}>Endorsement for UADCO</option>
                                    @elseif(auth()->user()->role == 4)
                                        <option value="6" {{ $proposal->action === 6 ? 'selected' : '' }}>Approval for UACAD</option>
                                        <option value="7" {{ $proposal->action === 7 ? 'selected' : '' }}>Approval for UADCO</option>
                                        <option value="3" {{ $proposal->action === 3 ? 'selected' : '' }}>Endorsement for BOR</option>
                                    @elseif(auth()->user()->role == 5)
                                        <option value="8" {{ $proposal->action === 8 ? 'selected' : '' }}>BOR Approval</option>
                                        <option value="9" {{ $proposal->action === 9 ? 'selected' : '' }}>BOR Confirmation</option>
                                        <option value="10" {{ $proposal->action === 10 ? 'selected' : '' }}>BOR Information</option>
                                    @endif
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
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary d-flex gap-2" id="updateProposalSec"><i class='bx bx-save'></i> Save Changes</button>
                            <!-- <button class="btn btn-secondary">Save Changes</button> -->
                        </div>
                    </form>
                </div>
            </div>
            <div class="">
                <h6 class="">PROPOSAL FILES</h6>
                <div class="nav-align-top mb-6">
                    <ul class="nav nav-pills mb-4 nav-fill" role="tablist">
                        <li class="nav-item mb-1 mb-sm-0">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#recent-files" aria-controls="recent-files" aria-selected="true"><span class="d-flex align-items-center gap-2">
                            <i class='bx bx-file'></i>
                            <span class="d-none d-sm-block"> Latest Versions</span>
                             <!-- <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-success ms-1">3</span> -->
                        </button>
                        </li>
                        <li class="nav-item mb-1 mb-sm-0">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#archived-files" aria-controls="archived-files" aria-selected="false"><span class="d-flex align-items-center gap-2">
                            <i class='bx bx-file'></i>
                            <span class="d-none d-sm-block">Old Versions</span> 
                            <!-- <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1">3</span> -->
                        </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="recent-files" role="tabpanel">
                            <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <tr>
                                                <th style="width: 50px;">
                                                    <!-- <input type="checkbox" class="form-check-input">  -->
                                                </th>
                                                <th style="width: 350px;">File Name</th>
                                                <th style="width: 150px;">Status</th>
                                                <th style="width: 150px;">Version</th>
                                                <th style="width: 150px;">Actions</th>
                                            </tr>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $countLatestVersion = 0;
                                        @endphp
                                        @foreach ($proposal->files as $file)
                                            @if ($file->is_active == 1)
                                                @php
                                                    $countLatestVersion++;
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="form-check-input select-proposal-file" data-id="{{ $file->id }}" > </td>
                                                    <td>
                                                        <div style="width: 350px;">
                                                            <small class="text-wrap text-primary"  data-bs-toggle="modal" 
                                                            data-bs-target="#fileModal"
                                                            data-file-url="/storage/proposals/{{$file->file}}">{{ $file->file }} </small>
                                                        </div>
                                                    </td>
                                                    <td>{{ config('proposals.proposal_file_status.'.$file->file_status ) }}</td>
                                                    <td>Version {{ $file->version }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <button type="button" class="btn btn-sm  btn-danger delete-proposal" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-original-title="Delete File "
                                                            data-id="{{ $file->id }}" {{ (!in_array($proposal->status, [2,5,6]) && $proposal->is_edit_disabled  && $file->file_status) ? 'disabled' : '' }}
                                                            > 
                                                                <i class='bx bx-trash'></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        @if ($countLatestVersion == 0)
                                            <td colspan="4">
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
                        </div>
                        <div class="tab-pane fade" id="archived-files" role="tabpanel">
                        <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 350px;">File Name</th>
                                            <th style="width: 150px;">Version</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $countOldVersion = 0;
                                        @endphp
                                        @foreach ($proposal->files as $file)
                                            @if ($file->is_active == 0)
                                                @php
                                                    $countOldVersion++;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div style="width: 350px;">
                                                            <small class="text-wrap text-primary"  data-bs-toggle="modal" 
                                                            data-bs-target="#fileModal"
                                                            data-file-url="/storage/proposals/{{$file->file}}">{{ $file->file }} </small>
                                                        </div>
                                                    </td>
                                                    <td>Version {{ $file->version }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        @if ($countOldVersion == 0)
                                            <td colspan="4">
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
                    </div>
                </div>
            </div>
        </div> 
        <div class="col" >
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
                                    <div class="com-wrapper d-flex {{ $log->user_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                        <div class="{{ $log->user_id == auth()->id() ? 'sender' : 'receiver' }}">
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
                                        $meetingDateTime = \Carbon\Carbon::parse($meeting->meeting_date_time);
                                    @endphp

                                    <ul class="dropdown-menu">
                                        @foreach (array_slice(config('proposals.proposal_action'), 0, 7, true) as $index => $item)
                                            @php
                                                if(auth()->user()->role > 3){
                                                    if (in_array($index, [1])) {
                                                        continue;
                                                    }
                                                }
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
                                <button class="btn btn-{{ $meeting->status == 1 ? 'danger': 'primary' }} d-flex gap-2 text-nowrap" 
                                        id="updateProposalStatus" 
                                        data-id="{{ encrypt($proposal->id) }}" 
                                        {{ $meeting->status == 1 ? 'disabled' : '' }}>
                                    
                                    {!! $meeting->status == 1 ? "<i class='bx bxs-lock-alt'></i>" : "<i class='bx bxs-send'></i>" !!}
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
<script>
    var proposalStatus = @json(config('proposals.status'));
     
    $('#matter').on('change', function() {
        var matter = $(this).val();
        var subType = $('#sub_type');
        var actionSelect = $('#action');

        actionSelect.empty();

        if (matter == 1) {
            actionSelect.append(`
                @if (auth()->user()->role == 3)
                    <option value="4">Endorsement for Local ACAD</option>
                    <option value="1">Endorsement for UACAD</option>
                @endif
                @if (auth()->user()->role == 4)
                    <option value="6">Approval for UACAD</option>
                    <option value="3">Endorsement for BOR</option>
                @endif
                @if (auth()->user()->role == 5)
                    <option value="8">BOR Approval</option>
                @endif
            `);
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');
        } else if (matter == 2) {
            subType.prop('disabled', false);
            $('#subTypeContainer').css('display', 'block');

            actionSelect.append(`
                @if (auth()->user()->role == 3)
                    <option value="5">Endorsement for Local ADCO</option>
                    <option value="2">Endorsement for UADCO</option>
                @endif
                @if (auth()->user()->role == 4)
                    <option value="7">Approval for UADCO</option>
                    <option value="3">Endorsement for BOR</option>
                @endif
                @if (auth()->user()->role == 5)
                    <option value="8">BOR Approval</option>
                @endif
            `);
        }else if (matter == 3) {
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');

            actionSelect.append(`
                <option value="9">BOR Confirmation</option>
            `);
        }
        else if (matter == 4) {
            subType.prop('disabled', true);
            $('#subTypeContainer').css('display', 'none');

            actionSelect.append(`
                <option value="10">BOR Information</option>
            `);
        }
    });
</script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection