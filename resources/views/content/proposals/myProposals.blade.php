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
<div class="row">
  <!-- Single Card: Local Proposals -->
  <div class="col-lg-3  col-md-6 col-sm-6 mb-3">
    <div class="card" style="height: 100%;">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="text-heading mb-1">Proposals Submitted Locally</p>
            <div class="d-flex align-items-center mb-1">
              <h4 class="card-title mb-0 me-2">{{ $proposalCounts['local'] }}</h4>
              <span class="text-primary">Total Proposals</span>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
                <i class='bx bx-book-open' ></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Single Card: University Proposals -->
  <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
    <div class="card" style="height: 100%;">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="text-heading mb-1">Proposals Submitted to the University</p>
            <div class="d-flex align-items-center mb-1">
              <h4 class="card-title mb-0 me-2">{{ $proposalCounts['university'] }}</h4>
              <span class="text-info">Total Proposals</span>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
                <i class='bx bx-book-reader'></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- Single Card: Board of Regents Proposals -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="card" style="height: 100%;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="text-heading mb-1">Proposals Submitted to the BOR</p>
                        <div class="d-flex align-items-center mb-1">
                        <h4 class="card-title mb-0 me-2">{{ $proposalCounts['board'] }}</h4>
                        <span class="text-success">Total Proposals</span>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-success rounded p-2">
                            <i class='bx bxs-book-reader' ></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="card" style="height: 100%;">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex gap-2">
                    <div class="">
                        <p class="text-muted mb-2">Need to submit proposal? Click below to proceed.  <a href="{{route(    getUserRole().'.meetings')}}" class="">
                            Go to meetings <i class='bx bx-right-arrow-alt'></i>
                        </a></p>
                    </div>
                    <div class="" style="height: 60px; min-width: 130px; ">
                        <img src="{{ asset('assets/img/illustrations/illust_1.jpg') }}" style="height:100%; width:  100%; object-fit: cover; "alt="">
                    </div>
                </div>
             
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
            <h5 class="m-0">My Proposals</h5>
            <div>
                <div class="input-group input-group-merge">
                    <input type="text" class="form-control" id="proposalSearch" placeholder="Search...">    
                    <span  class="input-group-text">
                        <i class='bx bx-search' ></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-datatable pt-0">
            <div class="table-responsive text-nowrap">
                <table id="proposalTable" class="datatables-basic table table-striped">
                    <thead class="custom-tbl-header">
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
                        <!-- <tr>
                            <td valign="top" colspan="9" class="dataTables_empty">
                                <div class="alert alert-warning mt-3" role="alert">
                                    <i class="bx bx-info-circle"></i> You dont have proposal at the moment.
                                </div>
                            </td>
                        </tr> -->
                    @else
                        @foreach($proposals as $proposal)
                        <tr data-title="{{ $proposal->title }}">
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
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($proposal->proponents as $proponent)
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
                                <div style="min-width: 300px; max-width: 500px; white-space: wrap; ">
                                    <a style="color: #697A8D;" href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->id)]) }}" >{{ $proposal->title }}</a>
                                </div>
                            </td>
                            <td>
                                <!-- <span class="badge bg-label-{{ $actionColors[$proposal->type] ?? 'primary' }}" style="text-transform: none;">
                                    {{ config('proposals.matters.'.$proposal->type) }}
                                </span> -->
                                <span class="align-items-center d-flex gap-2"> 
                                    {!! $proposal->type == 1 ? "<i class='bx bx-book-content text-primary'></i> " : "<i class='bx bxs-book-content text-danger' ></i>" !!}

                                    {{ config('proposals.matters.'.$proposal->type) }}
                                </span>
                            </td>
                            <td> 
                                <span class="d-flex gap-2 align-items-center">
                                    <i class='bx bx-up-arrow-circle text-{{ $actionColors[$proposal->action] ?? 'primary' }}'></i>
                                    {{ config('proposals.requested_action.'.$proposal->action) }}
                                </span>
                            </td>
                            <td>{{config('meetings.level.'.$proposal->getCurrentLevelAttribute())}}</td>
                            <td>
                                <div style="min-width: 200px; ">
                                    <span class="mb-0 align-items-center d-flex w-100 text-wrap gap-2">
                                        <i class='bx bx-radio-circle-marked text-{{$statusClass}}'></i>
                                        {{ config('proposals.status.'.$proposal->status) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($proposal->files->count() > 0)
                                    <button class="btn btn-sm btn-primary d-flex gap-2 view-files"
                                            data-files="{{ json_encode($proposal->files) }}" 
                                            data-title="{{ $proposal->title }}">
                                        <i class='bx bx-file'></i> VIEW FILES
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-danger d-flex gap-2">
                                        <i class='bx bx-file'></i> NO FILES
                                    </button>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a class="action-btn success edit-proposal" 
                                    href="{{ $proposal->is_editable ? route(getUserRole().'.proposal.edit', ['proposal_id' => encrypt($proposal->id)]) : 'javascript:void(0);' }}"
                                    onclick="{{ $proposal->is_editable ? '' : 'cantEditWarning()' }}">
                                        <i class='bx bxs-edit'></i>
                                        <span class="tooltiptext">Edit</span>
                                    </a>
                                    
                                    <button class="action-btn danger {{ $proposal->is_editable ? 'delete-proposal' : 'cantDeleteWarning()' }}"
                                        data-id="{{ encrypt($proposal->id) }}"
                                        onclick="{{ $proposal->is_editable ? '' : 'cantDeleteWarning()' }}">
                                        <i class='bx bx-trash-alt'></i>
                                        <span class="tooltiptext">Delete</span>
                                    </button>
                                    
                                    <a class="action-btn primary" 
                                    href="{{ route(getUserRole().'.proposal.details', ['proposal_id' => encrypt($proposal->id)]) }}">
                                        <i class='bx bx-right-top-arrow-circle'></i>
                                        <span class="tooltiptext">View</span>
                                    </a>
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
<script>
    function cantEditWarning() {
        showAlert("warning", "Can't Edit!", "You can no longer edit this proposal.");
    }

    function cantDeleteWarning() {
        showAlert("warning", "Can't Delete!", "You can no longer delete this proposal.");
    }


    // DELETE PROPOSAL
    $(".delete-proposal").on('click', function(e){
        e.preventDefault();
        var proposal_id = $(this).data("id");
        var is_delete_disabled = $(this).data("deletable");
        var button = $(this); 

        console.log(proposal_id);
        if(is_delete_disabled){
            showAlert("danger", "Can't Delete!", "You can no longer delete this proposal.");
            return;
        }
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/proponents/proposal/delete',
                    type: "POST",
                    data: { proposal_id: proposal_id },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if(response.type == 'success'){
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your file has been deleted.",
                                icon: "success"
                            });
                            button.closest("tr").remove(); 
                        }else{
                            showAlert("danger", response.title, response.message);
                        }
                    },            
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                        let response = JSON.parse(xhr.responseText);
                        showAlert("danger", response.title, response.message);
                    }
                });
            }
        });
    });


</script>
<script src="{{asset('assets/js/proposal.js')}}"></script>
<script src="{{asset('assets/js/dataTable.js')}}"></script>
@endsection
