@extends('layouts/contentNavbarLayout')

@section('title', 'Proposals')

@section('content')
<!-- toaster message -->
@include('components.toastrprompts')
<!-- toaster message -->
<div class="bread-crumbs overflow-auto" style="max-width: 100%; white-space: nowrap;">
    <h5>Dashboard</h5>
    <div class="divider"></div>
    <a href="/">
        <i class='bx bx-home-alt' ></i>
    </a>
    <i class='bx bx-chevron-right' ></i>
    <a href="{{route(    getUserRole().'.meetings')}}">Meetings</a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#">Submit Proposals</a>
</div>
<div class="card p-4">
    <form action="{{ route(getUserRole().'.proposal.submit', ['meeting_id' => encrypt($meeting->id)]) }}" method="post" id="submitProposalFrm" meeting-id="{{encrypt($meeting->id)}}">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <h5 class="card-header p-0 mb-2">
                @if ($meeting->level === 0)
                    {{ config("meetings.council_types.local_level.{$meeting->council_type}") }}
                @endif
                @if ($meeting->level === 1)
                    {{ config("meetings.council_types.university_level.{$meeting->council_type}") }}
                @endif
                @if ($meeting->level === 2)
                    {{ config("meetings.council_types.board_level.{$meeting->council_type}") }}
                @endif
            </h5> 
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fw-light">{{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, l, h:i A') }}</span>

                @if ($meeting->modality == 1 || $meeting->modality == 3)
                <span> | Venue  at  {{$venue}}</span>
                @elseif ($meeting->modality == 2 || $meeting->modality == 3)
                <span> | Via {{ config('meetings.mode_if_online_types.'.$meeting->mode_if_online) }} - Online</span>
                @else
                    <span class="form-label m-0">Venue or platform not yet set</span>
                @endif
            </div>
        </div>

        @php 
            $counter = 1;
            $actionColors = ['secondary', 'success', 'warning', 'danger', 'info']; 
            $noProposals = !$administrativeProposals->count() && !$academicProposals->count();
        @endphp

        <div class="d-flex align-items-center justify-content-between mt-3 mb-3">
            <h5>List of Endorsed Proposals</h5>
            <button type="submit" class="btn btn-primary d-flex gap-2" id="submitSecBtn" {{$noProposals ? 'disabled' : ''}}>
                <i class='bx bx-send'></i>
                <span>Submit to {{auth()->user()->role == 3 ? 'University Council' : 'BOR'}}</span>
            </button> 
        </div>

        <!-- New Business Section -->
        <div class="mb-3">
            @foreach (['administrativeProposals' => 'Administrative Matters', 'academicProposals' => 'Academic Matters'] as $proposalType => $title)
                <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="6" class="p-4 text-primary">{{ $title }}</th>
                            </tr>
                            <tr>
                                <th style="width: 50px;">No.</th>
                                <th style="max-width: 500px;">Proposal Title</th>
                                <th style="width: 200px;">Presenters</th>
                                <th style="width: 150px;">Requested Action</th>
                                <th style="width: 150px;">Status</th>
                                <th style="width: 100px;">File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <script>
                                var proposalIds = @json($administrativeProposals->pluck('id')->merge($academicProposals->pluck('id')));
                            </script>
                            @if ($$proposalType->count() > 0)
                                @foreach ($$proposalType as $proposal)
                                    <tr>
                                        <td>{{ $counter }}</td>
                                        <td>
                                            <div style="min-width: 300px; max-width: 500px; white-space: wrap; ">
                                                <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->id)]) }}"  style="color: var(--bs-secondary-text-emphasis);">{{ $proposal->title }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!-- <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                                    @foreach ($proposal->proponentsList as $proponent)
                                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $proponent->name }}" class="avatar avatar-sm pull-up">
                                                            <img class="rounded-circle" src="{{ $proponent->image ?? '/default-avatar.png' }}" alt="Avatar">
                                                        </li>
                                                    @endforeach
                                                    <li><small class="ms-3 text-muted">{{ $proposal->proponentsList->count() }} presenters</small></li>
                                                </ul> -->
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
                                            <span class="badge bg-label-{{ $actionColors[$proposal->action] ?? 'primary' }}" style="text-transform: none;">
                                                {{ config('proposals.requested_action.'.$proposal->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-{{ $actionColors[$proposal->status] ?? 'primary' }}" style="text-transform: none;">
                                                {{ config('proposals.status.'.$proposal->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($proposal->files->count() > 0)
                                                <button class="btn btn-sm btn-success d-flex gap-2 view-files"
                                                        data-files="{{ json_encode($proposal->files) }}" 
                                                        data-title="{{ $proposal->title }}">
                                                    <i class='bx bx-file'></i> View Files
                                                </button>
                                            @else
                                                <span class="text-muted">No Files</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php $counter++; @endphp
                                @endforeach
                            @else
                                <tr>
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
            @endforeach
        </div>
    </form>

    <!-- Modal Files-->
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
</div>

<script src="{{ asset('assets/js/proposal.js') }}"></script>

@endsection
