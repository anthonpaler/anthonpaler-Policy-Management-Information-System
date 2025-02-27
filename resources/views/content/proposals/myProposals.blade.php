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
    <a href="#">My Proposals</a>
</div>
@php 
    $actionColors = [ 'secondary', 'primary', 'success', 'warning', 'info', 'danger']; 
@endphp 
<div class="card">
    <h5 class="card-header">My Proposals List</h5>
    <div class="card-body">
        <div class="card-datatable pt-0">
            <div class="table-responsive text-nowrap">
                <table id="proposalTable" class="datatables-basic table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Proponent/s</th>
                            <th style="max-width: 500px; ">
                                Proposal Title
                            </th>
                            <th>Type</th>
                            <th>Requested Action</th>
                            <th>Current Level</th>
                            <th>Current Status</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="">
                    @if ($proposals->isEmpty())
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-warning mt-3" role="alert">
                                    <i class="bx bx-info-circle"></i> You dont have proposal at the moment.
                                </div>
                            </td>
                        </tr>
                    @else
                        @foreach($proposals as $proposal)
                        <tr data-proponent="{{ $proposal->proponent }}" data-title="{{ $proposal->title }}">
                            <td>{{ $loop->iteration }}</td>
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
                                <div style="min-width: 300px; max-width: 500px; white-space: wrap; ">
                                    <a href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->id)]) }}" >{{ $proposal->title }}</a>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $actionColors[$proposal->type] ?? 'primary' }}" style="text-transform: none;">
                                    {{ config('proposals.matters.'.$proposal->type) }}
                                </span>
                            </td>
                            <td> {{ config('proposals.requested_action.'.$proposal->action) }}</td>
                            <td>{{config('meetings.level.'.$proposal->level)}}</td>
                            <td>
                                <div style="width: 230px; white-space: nowrap; ">
                                    <small class="mb-0 align-items-center d-flex w-px-100">
                                        <i class='bx bx-radio-circle-marked text-{{ $actionColors[$proposal->status] ?? 'primary' }}'></i>
                                        {{ config('proposals.status.'.$proposal->status) }}
                                    </small>
                                </div>
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
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn p-0 hide-arrow delete-proposal text-{{ $proposal->is_edit_disabled ? 'light' : 'secondary'}}" data-id="{{ encrypt($proposal->id) }}" data-deletable="{{ $proposal->is_edit_disabled }}">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->id)]) }} ">
                                                <i class="fa-regular fa-eye me-3"></i>View Details
                                            </a>
                                            
                                            @if(in_array($proposal->status, [2,5,6]))
                                                <a class="dropdown-item" href="{{ route(getUserRole().'.proposal.edit', ['proposal_id' => encrypt($proposal->id)]) }}">
                                                    <i class='bx bx-right-arrow-circle me-3'></i>Resubmit Proposal
                                                </a>
                                            @endif
                                            @if(!$proposal->is_edit_disabled)
                                                <a class="dropdown-item" href="{{ route(getUserRole().'.proposal.edit', ['proposal_id' => encrypt($proposal->id)]) }}">
                                                    <i class="bx bx-edit-alt me-3"></i>Edit Proposal
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
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
<script src="{{asset('assets/js/proposal.js')}}"></script>
<script src="{{asset('assets/js/pagination.js')}}"></script>
@endsection
