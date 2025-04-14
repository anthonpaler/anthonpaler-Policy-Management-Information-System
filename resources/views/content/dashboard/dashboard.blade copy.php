@extends('layouts/contentNavbarLayout')

@section('title', 'Policy Management Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

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
              <span>{{ config('usersetting.role.'.session('user_role')) }}</span>
            </div>

            <h5>DASHBOARD</h5>
          </b>
        </div>
        <div class="p-4">

        </div>
      </div>
    </div>
    {{-- <hr>
    <p class="font-medium-3 text-bold-500 d-flex align-items-center gap-3"><i class="bx bxs-megaphone text-danger"></i> ANNOUNCEMENTS <i class="text-danger bx bxs-megaphone"></i></p> --}}
    {{-- <div class="card mt-3">
      <div class="card-body">
        <p>No announcements have been made yet.</p>
      </div>
    </div> --}}
    {{-- <div class="row">
      <div class="col">
        <div class="card mt-3">
          <div class="card-header">
            <h5 class="m-0">University Latest Meetings</h5>
          </div>
          <div class="card-body">
            <div class="border rounded p-2 d-flex align-items-center  justify-content-between  flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="card-icon">
                  <span class="badge bg-label-danger rounded p-2">
                    <i class='bx bxs-book-content'></i>
                  </span>
                </div>
                <div class="d-flex flex-column gap-1">
                  <h6 class="m-0">1st Quarter Local Academic Meeting 2025</h6>
                  <small class="d-flex align-items-center gap-1 text-danger">Closed</small>
                </div>
              </div>
              <div class="d-flex flex-column gap-2 align-items-end">
                <small>April 23, 2025</small>
                <div class="d-flex align-items-center gap-2">
                  <h5 class="m-0">43 proposals</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mt-3">
         <div class="card-body">
          <h5 class="mb-4">LATEST LOCAL COUNCIL MEETINGS</h5>
          <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center" style="width: 80px; height: 100%;">
              <div class="d-flex flex-column gap-2 align-items-center justify-content-center">
                <h1 class="m-0 text-primary">43</h1>
                <small class="text-primary">proposals</small>
              </div>
            </div>
            <div class="d-flex flex-column gap-3">
              <div class="d-flex align-items-center gap-2">
                <span class="text-muted ">1st Quarter 2025</span>
                <span class="badge bg-label-danger">Closed</span>
                <span class="badge bg-label-primary">Has OOB</span>
              </div>
              <h6 class="m-0">Local Academic Council Meeting</h6>
              <span class="">Thursday, April 30, 2025</span>
            </div>
          </div>
         </div>
        </div>
      </div>
    </div> --}}
    <div class="row">
      <div class="col">
        <div class="card mt-3">
          <div class="card-body">
            <div class="d-flex justify-content-between gap-3">
              <h5>Latest Local Meeting</h5>
              <div class="d-flex flex-column align-items-end">
                <h6 class="m-0">2025</h6>
                <small class="text-muted">1st Quarter</small>
              </div>
            </div>
            <div class="mini-card">
              <div class="d-flex justify-content-between">
                
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
      </div>
    </div>
  </div>
</div>
@endsection
