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
                            <small class="text-light fw-medium">Proposal Title</small>
                            <h6 class="mb-0">{{$proposal->title}}</h6>
                        </div>
                        <small class="text-light fw-medium">Proponents</small>
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
                        </button>
                        </li>
                        <li class="nav-item mb-1 mb-sm-0">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#archived-files" aria-controls="archived-files" aria-selected="false"><span class="d-flex align-items-center gap-2">
                            <i class='bx bx-file'></i>
                            <span class="d-none d-sm-block">Old Versions</span> 
                        </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="recent-files" role="tabpanel">
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
                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" class="form-check-input select-proposal-file" data-id="{{ $file->id }}" > 
                                                <small>SELECT</small>
                                            </div>
                                           
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
                                        </div>
                                    @endif
                                @endforeach
                                @if ($countLatestVersion == 0)
                                    <td colspan="4">
                                        <div
                                            class="alert alert-info"
                                            role="alert"
                                        >
                                            <p>No files submitted</p>
                                        </div>
                                        
                                    </td>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="archived-files" role="tabpanel">
                            <div class="proposal-file-con mb-2">
                                @php
                                    $countOldVersion = 0;
                                @endphp
                                @foreach ($proposal->files as $file)
                                    @if ($file->is_active == 0)
                                        @php
                                            $countOldVersion++;
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
                                        </div>
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
                                    <div class="com-wrapper d-flex {{ $log->employee_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                        <div class="{{ $log->employee_id == auth()->id() ? 'sender' : 'receiver' }}">
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
<script>
    var proposalStatus = @json(config('proposals.status'));
</script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection