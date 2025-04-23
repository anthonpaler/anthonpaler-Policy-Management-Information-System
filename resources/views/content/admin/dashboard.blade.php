@extends('layouts/contentNavbarLayout')

@section('title', 'Policy Management Dashboard')



@section('page-script')
<script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
@endsection

@section('content')
<div class="row">
  <div class="col">
    <!-- Welcome Card -->
    <div class="card">
      <div class="card-content dashboard-bg-con">
        <div class="dashboard-bg">
          <img src="{{asset('assets/img/backgrounds/slsu_bg_2.jpeg') }}"  class="img-fluid rounded-top user-timeline-image" alt="user timeline image">
        </div>
        <div class="user-info-dashboard d-flex gap-3 p-3">
          <div class="user-profile">
              <img src="{{ auth()->user()->image }}" class="user-profile-image rounded" alt="user profile image" >
          </div>
          <b class="user-profile-text ml-1 text-dark">
            <div>
              <h6 class="">{{ auth()->user()->name }}</h6>
              <span>{{ config('usersetting.role.'.auth()->user()->role) }}</span>
            </div>
            
            <h5>DASHBOARD</h5>
          </b>
        </div>
        <div class="p-1">

        </div>
      </div>
    </div>
    <hr>
    <p class="font-medium-3 text-bold-500 d-flex align-items-center gap-3"><i class="bx bxs-megaphone text-danger"></i> ANNOUNCEMENTS <i class="text-danger bx bxs-megaphone"></i></p>
    <div class="card mt-3 mb-3">
      <div class="card-body">
        <p>No announcements have been made yet.</p>
      </div>
      </div>

<div class="row g-6 mb-6">

        <div class="col-sm-6 col-xl-3">
          <div class ="card">
              <div class ="card-body">

            <div class="d-flex align-items-start justify-content-between">
        <div class="content-left">
                    <span class="text-heading">Total Users</span>
                    <div class="d-flex align-items-center my-1">
                      <h4 class="mb-0 me-2"></h4>
                      <p class="text-primary mb-0">{{ $totalUsers }}</p>
                    </div>
                  </div>
                
                </div>
          </div>
      </div>
  </div>

        <div class="col-sm-6 col-xl-3">
              <div class ="card">
                  <div class ="card-body">

          <div class="d-flex align-items-start justify-content-between">
              <div class="content-left">
                      <span class="text-heading">Academic Council Members</span>
                      <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2"></h4>
                        <p class="text-primary mb-0" id="academicCount">{{ $academicCouncilCount }}</p>
                      </div>
                    </div>
                    <div class="avatar">
                      <span class="avatar-initial rounded bg-label-info view-academic-members" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#councilModal" data-council-type="academic">
                        <i class="icon-base bx bx-user-plus icon-lg showMemberInfo" style="cursor: pointer;"></i>
                      </span>
                    </div>
                  </div>
            </div>
        </div>
    </div>


  <div class="col-sm-6 col-xl-3">
    <div class ="card">
       <div class ="card-body">

            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                        <span class="text-heading">Administrative Council Members</span>
                        <div class="d-flex align-items-center my-1">
                          <h4 class="mb-0 me-2"></h4>
                          <p class="text-primary mb-0" id="administrativeCount">{{ $administrativeCouncilCount }}</p>
                        </div>
                      </div>
                      <div class="avatar">
                        <span class="avatar-initial rounded bg-label-info view-admin-members" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#councilModal" data-council-type="administrative">
                        <i class="icon-base bx bx-user-plus icon-lg"></i>
                        </span>
                      </div>
                    </div>
              </div>
          </div>
      </div>


      <div class="col-sm-6 col-xl-3">
          <div class ="card">
       <div class ="card-body">

            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                        <span class="text-heading">Joint Cuncil Member</span>
                        <div class="d-flex align-items-center my-1">
                          <h4 class="mb-0 me-2"></h4>
                          <p class="text-primary mb-0" id="jointCount">{{ $jointCount }}</p>
                        </div>
                      </div>
                      <div class="avatar">
                       <span class="avatar-initial rounded bg-label-info view-joint-members" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#councilModal" data-council-type="joint">
                        <i class="icon-base bx bx-user-plus icon-lg"></i>
                        </span>
                      </div>
                    </div>
              </div>
          </div>
      </div>

 


</div>


<!-- Reusable Council Modal -->
<div class="modal fade" id="councilModal" tabindex="-1" aria-labelledby="councilModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="councilModalLabel">Council Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div class="mb-3">
          <label for="councilEmail" class="form-label">Email</label>
          <input type="text" class="form-control" id="councilEmail">
        </div>
        <div class="mb-3">
          <label for="councilCampus" class="form-label">Campus</label>
          <input type="text" class="form-control" id="councilCampus" readonly>
        </div>
        <button class="btn btn-primary" id="addCouncilMember">Add Member</button>
      </div>

    </div>
  </div>
</div>

{{-- 
<!-- Add Academic Council Member Modal -->
<div class="modal fade" id="academicModal" tabindex="-1" aria-labelledby="academicModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="academicModalLabel">Academic Council Members</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div class="mb-3">
          <label for="academicEmail" class="form-label">Email</label>
          <input type="text" class="form-control" id="academicEmail" name="academicEmail">
        </div>
        <div class="mb-3">
          <label for="academicCampus" class="form-label">Campus</label>
      <input type="text" class="form-control" id="academicCampus" name="academicCampus" readonly>
        </div>

        <button class="btn btn-primary" id="addAcademicMember">Add Member</button>

      </div>



    </div>
  </div>
</div>


 <!-- Add Administrative Council Member Modal -->
<div class="modal fade" id="administrativeModal" tabindex="-1" aria-labelledby="administrativeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Administrative Council Members</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="adminEmail" class="form-label">Email</label>
          <input type="text" class="form-control" id="adminEmail">
        </div>
        <div class="mb-3">
          <label for="adminCampus" class="form-label">Campus</label>
          <input type="text" class="form-control" id="adminCampus" name="adminCampus">
        </div>
        <button class="btn btn-primary" id="addAdminMember">Add Member</button>
      </div>
    </div>
  </div>
</div>



<!-- Add Joint Council Member Modal -->
<div class="modal fade" id="jointModal" tabindex="-1" aria-labelledby="jointModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" >Joint Council Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="jointEmail" class="form-label">Email</label>
          <input type="text" class="form-control" id="jointEmail">
        </div>
        <div class="mb-3">
          <label for="jointCampus" class="form-label">Campus</label>
          <input type="text" class="form-control" id="jointCampus" name="jointCampus">
        </div>
        <button class="btn btn-primary" id="addJointMember">Add Member</button>
      </div>
    </div>
  </div>
</div> --}}


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>

 function refreshCouncilCounts() {
        $.ajax({
            url: "{{ route('get.council.counts') }}",
            method: "GET",
            success: function (data) {
                $('#academicCount').text(data.academic);
                $('#administrativeCount').text(data.administrative);
                $('#jointCount').text(data.joint);
            },
            error: function (xhr) {
                console.error("Failed to fetch council counts:", xhr);
            }
        });
    }


$(document).ready(function () {
    let currentCouncilType = null;
    let tagify;
  refreshCouncilCounts();
    // When modal is triggered
      $('[data-council-type]').on('click', function () {
        currentCouncilType = $(this).data('council-type');
        let title = currentCouncilType.charAt(0).toUpperCase() + currentCouncilType.slice(1) + " Council Member";
        $('#councilModalLabel').text(title);

        // Reset fields
        $('#councilEmail').val('');
        $('#councilCampus').val('').removeData('campus-id');

        // Destroy and reinitialize tagify
        if (tagify) tagify.destroy();
        tagify = new Tagify(document.querySelector('#councilEmail'), {
            whitelist: [],
            dropdown: {
                enabled: 1,
                position: "text",
                maxItems: 10
            }
        });

        tagify.on('input', function (e) {
            $.ajax({
                url: "{{ route('search.hrmis.email') }}",
                type: "GET",
                data: { query: e.detail.value },
                success: function (data) {
                    tagify.settings.whitelist = data.map(item => item.EmailAddress);
                    tagify.dropdown.show.call(tagify, e.detail.value);
                }
            });
        });

        tagify.on('add', function (e) {
            let selectedEmail = e.detail.data.value;
            $.ajax({
                url: "{{ route('search.hrmis.email') }}",
                type: "GET",
                data: { query: selectedEmail },
                success: function (data) {
                    let match = data.find(item => item.EmailAddress === selectedEmail);
                    if (match) {
                        $('#councilCampus').val(match.CampusName);
                        $('#councilCampus').data('campus-id', match.CampusID);
                    }
                }
            });
        });

        tagify.on('remove', function () {
            $('#councilCampus').val('').removeData('campus-id');
        });
    });

    $('#addCouncilMember').on('click', function () {
        refreshCouncilCounts();
        let email = tagify.value[0]?.value;
        let campusId = $('#councilCampus').data('campus-id');

        if (!email || !campusId) {
            alert('Please select a valid email from the suggestions.');
            return;
        }

        let routeMap = {
            academic: "{{ route('add.academic.member') }}",
            administrative: "{{ route('add.administrative.member') }}",
            joint: "{{ route('add.joint.member') }}"
        };

        $.ajax({
            url: routeMap[currentCouncilType],
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                email: email,
                campus: campusId
            },
            success: function (response) {
                alert(response.message);
                tagify.removeAllTags();
                $('#councilCampus').val('').removeData('campus-id');
                $('#councilModal').modal('hide');
            },
            error: function (xhr) {
                alert('Something went wrong: ' + xhr.responseText);
            }
        });
    });
});
</script>


@endsection
