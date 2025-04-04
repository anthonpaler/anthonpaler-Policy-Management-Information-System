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
    <a href="{{ route(    getUserRole().'.meetings') }}">Meetings</a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#">Generate OOB</a>
</div>

<div class="card p-4">
    <form action="{{ route(getUserRole().'.order_of_business.generate', ['level' => $meeting->getMeetingLevel(), 'meeting_id' => encrypt($meeting->id)]) }}" method="post" id="oobFrm" meeting-id="{{encrypt($meeting->id)}}">
        <div class="d-flex flex-column justify-content-center align-items-center text-center">
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
                <span> | Venue  at  {{ $meeting->venue ?? 'Not Set' }}</span>
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
        <div class="mt-3 mb-3">
          <label class="form-label">1. Preliminaries</label>
          <textarea  name="preliminaries" id="preliminaries">
            <p style="line-height: 1;"> 1.1. Call to Order
            </p>
            <p style="line-height: 1;"> 1.2. Prayer
            </p>
            <p style="line-height: 1;"> 1.3. Acceptance of the Provisional Agenda
            </p>
            <p style="line-height: 1;"> 1.4. Reading and Ratification of the Previous Minutes
            </p>
            <p style="line-height: 1;"> 1.5. Chairperson’s Time</p>
          </textarea>
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
        <div class="mb-3">
            <label class="form-label">2. New Business</label>

            @php
                $counter = 1;
                $actionColors = ['secondary', 'success', 'warning', 'danger', 'info'];
                $noProposals = collect($categorizedProposals)->flatten()->isEmpty();
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
                                                    @foreach ($proposal->proposal->proponents as $proponent)
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img class="rounded-circle avatar-sm" src="{{ $proponent->image && trim($proponent->image) !== '' ? $proponent->image : asset('assets/img/avatars/default-avatar.jpg') }}
            " alt="Avatar">
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
                                            @if($proposal->proposal->files->count() > 0)
                                                <button class="btn btn-sm btn-primary d-flex gap-2 view-files"
                                                        data-files="{{ json_encode($proposal->proposal->files) }}"
                                                        data-title="{{ $proposal->proposal->title }}">
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
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary d-flex gap-2" id="generateOOBBtn">
                <i class='bx bx-send'></i>
                <span>Generate OOB</span>
            </button>
        </div>

    </form>
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
</div>
{{-- For Jodit Text Editor --}}
<script
type="text/javascript"
src="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.js"
></script>
<script>
    // For Jodit Editor in Pre
    Jodit.make('#preliminaries');

    $(document).on('click', '.view-files', function (e) {
        e.preventDefault();
        var files = $(this).data("files");
        var title = $(this).data("title");

        console.log(files);

        if (!files || files.length === 0) {
            $("#modalFiles").html('<p class="text-danger">No files available.</p>');
        } else {
            let fileListHtml = `
                <div class="">
                    <div class="d-flex flex-column">
                        <span class="form-label">Title:</span>
                        <h6 id="modal-title">${title || 'No Title Available'}</h6>
                    </div>
                    <div class="">
                        <span class="form-label">Files:</span>
                        <div class="d-flex flex-column gap-2 mt-2">
            `;

            $.each(files, function (index, fileObj) {
                if(fileObj.is_active == true){
                    fileListHtml += `
                    <a href="#" class="form-control d-flex align-items-center gap-2 view-file-preview" style="text-transform: none;"
                    data-bs-toggle="modal"
                    data-bs-target="#fileModal"
                    data-file-url="/storage/proposals/${fileObj.file}" >
                        <span>${fileObj.order_no}. </span><i class='bx bx-file-blank'></i><span>${fileObj.file}</span>
                    </a>`;
                }
            });

            fileListHtml += `</div></div></div>`;
            $("#modalFiles").html(fileListHtml);
        }

        var myModal = new bootstrap.Modal(document.getElementById('proposalFIleModal'));
        myModal.show();
    });

    $(document).on('click', '.view-file-preview', function (e) {
        e.preventDefault();
        const fileUrl = $(this).data('file-url');
        $('#fileIframe').attr('src', fileUrl);

        var fileModal = new bootstrap.Modal(document.getElementById('fileModal'));
        fileModal.show();
    });

    $('#fileModal').on('show.bs.modal', function () {
        $('#proposalFIleModal').addClass('d-block');
    });

    $('#fileModal').on('hidden.bs.modal', function () {
        $('#proposalFIleModal').removeClass('d-block');
        $('#proposalFIleModal').modal('show');
    });

    $('#proposalFIleModal').on('hidden.bs.modal', function () {
        setTimeout(function() {
            if ($('.modal-backdrop').length > 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            }
        }, 200);
    });

    </script>
<script src="{{asset('assets/js/orderOfBusiness.js')}}"></script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
@endsection
