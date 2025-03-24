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
    <a href="#">Submit Proposals</a>
</div>
<div class="card p-4">
    <form action="{{ route(getUserRole().'.proposal.submit', ['level' => $meeting->getMeetingLevel(),'meeting_id' => encrypt($meeting->id)]) }}" method="post" id="submitProposalFrm" meeting-id="{{encrypt($meeting->id)}}">
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
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fw-light">{{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, l, h:i A') }}</span>

                @if ($meeting->modality == 1 || $meeting->modality == 3)
                <span> | Venue  at  {{$meeting->venue->name}}</span>
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
            $noProposals = collect($categorizedProposals)->flatten()->isEmpty();


            if($meeting->getMeetingCouncilType() == 1){
                $allProposalIds = collect($categorizedProposals)->flatten()->pluck('local_proposal_id');  // PROPOSAL ID FROM LOCAL MEETING AGENDA TO BE PASSED TO UNIVERSITY     
            }elseif($meeting->getMeetingCouncilType() == 2){
                $allProposalIds = collect($categorizedProposals)->flatten()->pluck('university_proposal_id');   // PROPOSAL ID FROM UNIVERSITY MEETING AGENDA TO BE PASSED TO BOR     
            }
        @endphp

        <div class="d-flex align-items-center justify-content-between mt-3 mb-3">
            <h5>List of Endorsed Proposals</h5>
            <button type="submit" class="btn btn-primary d-flex gap-2" id="submitSecBtn" {{$noProposals ? 'disabled' : ''}}>
                <i class='bx bx-send'></i>
                <span>Submit to {{session('user_role') == 3 ? 'University Council' : 'BOR'}}</span>
            </button> 
        </div>
    
        <div class="mb-3">
            @if (!count($allProposalIds) > 0)
                <div class="alert alert-info m-0" role="alert">
                    <i class="bx bx-info-circle"></i> No proposals for endorsement at the moment.
                </div>
            @else
                @foreach ($matters as $type => $title)
                    @if (isset($categorizedProposals[$type]) && $categorizedProposals[$type]->count() > 0)
                        <div class="table-responsive text-nowrap mb-4">

                            <table class="table table-bordered">
                                <thead>
                                    <tr class="" style="background-color: #E5EDFC; !important">
                                        <th colspan="6" class="p-4 text-primary">{{ $title }}</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 50px;">No.</th>
                                        <th style="width: 700px;">Title of the Proposal</th>
                                        <th style="width: 200px;">Presenters</th>
                                        <th style="width: 150px;">Requested Action</th>
                                        <th style="width: 150px;">Status</th>
                                        <th style="width: 100px;">File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($categorizedProposals[$type]->count() > 0)
                                        @foreach ($categorizedProposals[$type] as $proposal)
                                            <tr>
                                                <td>{{ $counter }}</td>
                                                <td>
                                                    <div style="min-width: 300px; max-width: 700px; white-space: wrap; ">
                                                        <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->proposal->id)]) }}" >{{ $proposal->proposal->title }}</a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex flex-column gap-3">
                                                            @foreach ($proposal->proposal->proponents as $proponent)
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
                                                    <span class="align-items-center d-flex gap-2"> 
                                                        <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal->proposal->action] ?? 'primary' }}'></i>
                                                        {{ config('proposals.requested_action.'.$proposal->proposal->action) }}
                                                    </span>
                                            
                                                </td>
                                                <td>
                                                    <div style="width: 150px; white-space: nowrap; ">
                                                        <span class="mb-0 align-items-center d-flex w-px-100 gap-1">
                                                            <i class='bx bx-radio-circle-marked text-{{ $actionColors[$proposal->proposal->status] ?? 'primary' }}'></i>
                                                            {{ config('proposals.status.'.$proposal->proposal->status) }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($proposal->proposal->files->count() > 0)
                                                        <button class="btn btn-sm btn-success d-flex gap-2 view-files"
                                                                data-files="{{ json_encode($proposal->proposal->files) }}" 
                                                                data-title="{{ $proposal->proposal->title }}">
                                                            <i class='bx bx-file'></i> VIEW FILES
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-danger d-flex gap-2">
                                                            <i class='bx bx-file'></i> NO FILES
                                                        </button>
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
                    @endif
                @endforeach
            @endif
           
            <script>
                var endorsedProposalIds = @json($allProposalIds);
                console.log('Endorsed Proposal'.endorsedProposalIds);
            </script>
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
