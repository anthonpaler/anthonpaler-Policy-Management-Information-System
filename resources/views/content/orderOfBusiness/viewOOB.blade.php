@extends('layouts/contentNavbarLayout')

@section('title', 'Order of Business')

@section('content')
<div class="bread-crumbs overflow-auto" style="max-width: 100%; white-space: nowrap;">
    <h5>Dashboard</h5>
    <div class="divider"></div>
    <a href="/">
        <i class='bx bx-home-alt' ></i>
    </a>
    <i class='bx bx-chevron-right' ></i>
    <a href="{{ route(getUserRole().'.order-of-business')}}" >Order of Business</a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#" >Order of Business Information</a>
</div>
<style>
    
</style>
<div class="card p-4">

    <div class="mt-3">
        <button type="button" id="exportOOB" class="btn btn-secondary d-flex gap-2 {{$orderOfBusiness->status == 0 ? 'd-none' : ''}}">
            <a href="{{ route('order_of_business.pdf', ['oob_id' => encrypt($orderOfBusiness->id)]) }}" class="text-white d-flex align-items-center gap-2" target="_blank"><i class='bx bx-export'></i> Export OOB</a>
        </button>
    </div>
    
    <form action="{{ route(getUserRole().'.order_of_business.save', ['level' => $meeting->getMeetingLevel(), 'oob_id' => encrypt($orderOfBusiness->id)]) }}" method="post" id="oobFrm" meeting-id="{{encrypt($orderOfBusiness->meeting->id)}}">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <h4 class="card-header p-0 mb-2">
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
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center ">
                <span class="text-muted fw-light text-center">{{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, l, h:i A') }}</span>

                @if ($meeting->modality == 1 || $meeting->modality == 3)
                <span> | Venue  at  {{$meeting->venue->name}}</span>
                @elseif ($meeting->modality == 2 || $meeting->modality == 3)
                <span> | Via {{ config('meetings.mode_if_online_types.'.$meeting->mode_if_online) }} - Online</span>
                @else
                    <span class="form-label m-0">Venue or platform not yet set</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 mt-3 mb-3">
                <h5 class="card-header p-0 ">ORDER OF BUSINESS</h5> 
            </div>
        </div>

        <!-- Preliminaries Section -->
        <div class="mb-3">
            <label class="form-label">1. Preliminaries</label>
            <div class="input-group input-group-merge">
            <textarea
        id="preliminaries" 
        class="form-control"
        placeholder="Enter preliminaries."
        aria-label="Enter preliminaries."
        name="preliminaries"
        rows="5"
        {{ $orderOfBusiness->status == 1 || getUserRole() == 'proponent' ? 'disabled' : '' }}
        >
        {{$orderOfBusiness->preliminaries}}</textarea>

                @error('preliminaries')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <!-- Modal -->
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
        <!-- New Business Section -->
        <!-- New Business Section -->
        <div class="mb-3">
            <label class="form-label">2. New Business</label>

            @php 
                $counter = 1; 
                $actionColors = ['secondary', 'success', 'warning', 'danger', 'info']; 
                $noProposals = collect($categorizedProposals)->flatten()->isEmpty();

                $allProposalIds = collect($categorizedProposals)->flatten()->pluck('id');

            @endphp

            <!-- Loop through proposals and display tables -->
            @foreach ($matters as $type => $title)
                @if ($categorizedProposals[$type]->count() > 0)
                    <div class="table-responsive text-nowrap mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="" style="background-color: #E5EDFC; !important">
                                    <th colspan="5" class="p-4 text-primary">{{ $title }}</th>
                                </tr>
                                <tr>
                                    <th style="width: 50px;">No.</th>
                                    <th style="width: 700px;">Title of the Proposal</th>
                                    <th style="width: 200px;">Presenters</th>
                                    <th style="width: 150px;">Requested Action</th>
                                    <th style="width: 100px;">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($categorizedProposals[$type]->count() > 0)
                                    @foreach ($categorizedProposals[$type] as $proposal)
                                    <tr>
                                        <td>2.{{ $counter }}</td>
                                        <td>
                                            <div style="min-width: 300px; max-width: 700px; white-space: wrap; ">
                                                <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->id)]) }}" >{{ $proposal->proposal->title }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
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
                                            <span class="align-items-center d-flex gap-2"> 
                                                <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal->proposal->action] ?? 'primary' }}'></i>
                                                {{ config('proposals.requested_action.'.$proposal->proposal->action) }}
                                            </span>
                                    
                                        </td>
                                        <td>
                                            @if($proposal->files->count() > 0)
                                                <button class="btn btn-sm btn-primary d-flex gap-2 view-files"
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
                @endif
            @endforeach
            <script>
                var endorsedProposalIds = @json($allProposalIds);
            </script>
        </div>
        
        @if(!in_array(auth()->user()->role, [0,1,2]))
        <div class="d-flex gap-3 align-items-center">
            <div class="mt-3">
    <button type="submit" class="btn btn-primary d-flex gap-2" id="saveOOBBtn" {{$orderOfBusiness->status == 1 ? 'disabled' : ''}}>
                    <i class='bx bx-save' ></i>
                    <span>Save Changes</span>
                </button> 
            </div>
            <div class="mt-3">
                <button type="buttton" class="btn btn-success d-flex gap-2" id="disseminateOOBBtn" data-id="{{encrypt($orderOfBusiness->id)}}" data-action = "{{ route(getUserRole().'.dissemenate.order_of_business', ['oob_id' => encrypt($orderOfBusiness->id)]) }}" {{$orderOfBusiness->status == 1 ? 'disabled' : ''}}>
                    <i class='bx bx-send'></i>
                    <span>Disseminate OOB</span>
                </button> 
            </div>
        </div>
        @endif
    </form>
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
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const downloadButton = document.querySelector("[aria-label='Download']");
        if (downloadButton) {
            downloadButton.addEventListener("click", function() {
                alert("Clicked");
            });
        }
    });
</script>

<script src="{{asset('assets/js/orderOfBusiness.js')}}"></script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection
