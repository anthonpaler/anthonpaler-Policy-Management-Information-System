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
    <div class="row">
      <div class="col-12 col-xl-4 col-md-6">
        <div class="card h-100 mt-4">
          <div class="card-body">
            <div class="bg-label-primary rounded-3 text-center mb-4 pt-6">
              <img class="img-fluid" src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/illustrations/sitting-girl-with-laptop.png" alt="Card girl image" style="width: 52%;">
            </div>
            <h5 class="mb-2">Latest BOR Meeting</h5>
            <p>Next Generation Frontend Architecture Using Layout Engine And React Native Web.</p>
            <div class="row mb-4 g-3">
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-calendar"></i></span>
                  </div>
                  <div>
                    <h6 class="mb-0 text-nowrap">17 Nov 23</h6>
                    <small>Date</small>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-time-five"></i></span>
                  </div>
                  <div>
                    <h6 class="mb-0 text-nowrap">32 minutes</h6>
                    <small>Duration</small>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 text-center">
              <a href="javascript:void(0);" class="btn btn-primary w-100 d-grid">Join the event</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mt-4">
          .
        </div>
      </div>
      <div class="col">
        <div class="card mt-4">
          .
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
