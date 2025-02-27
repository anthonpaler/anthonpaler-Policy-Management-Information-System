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
    <div class="card mt-3">
      <div class="card-body">
        <p>No announcements have been made yet.</p>
      </div>
    </div>
    <!-- <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h3 class="card-title text-primary">Welcome Back, {{ auth()->user()->name }}! 🎉</h3>
            <p class="mb-4">
              Stay informed and manage policies efficiently. Use this dashboard to track updates, review compliance, and access key insights.
            </p>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" 
              alt="Policy Management Overview"
              data-app-dark-img="illustrations/man-with-laptop-dark.png" 
              data-app-light-img="illustrations/man-with-laptop-light.png">
          </div>
        </div>
      </div>
    </div> -->

    <!-- @php
      $userRole = auth()->user()->role;
    @endphp -->

    <!-- Admin & Authorized Roles -->
    <!-- @if (in_array($userRole, [0, 1, 2, 6]))
      <div class="card mt-3">
        <div class="card-body">
          <div class="alert alert-success">
            <p>The Meetings are Available</p>
          </div>
        </div>
      </div>
    @else
      <div class="card mt-3">
        <div class="card-body">
          <div class="alert alert-warning">
            <p>The Meetings are not Available</p>
          </div>
        </div>
      </div> -->
      
      <!-- Meeting Notification -->
      <!-- @if(isset($meetingCreatedBySecretary) && $meetingCreatedBySecretary)
        <div class="card mt-3">
          <div class="card-body">
            <div class="alert alert-warning">
              <p><strong>Notification:</strong> A new meeting has been scheduled by the local secretary.</p>
            </div>
          </div>
        </div>
      @else -->
        <!-- No Meeting Notification -->
        <!-- <div class="card mt-3">
          <div class="card-body">
            <div class="alert alert-warning">
              <p><strong>Notification:</strong> No meeting set by Local Secretary</p>
            </div>
          </div>
        </div>
      @endif
    @endif -->

    <!-- Display Meetings as Announcements -->
    <!-- @if(isset($meetings) && $meetings->count() > 0)
      @foreach($meetings as $meeting)
        <div class="card mt-3">
          <div class="card-body">
            <div class="alert alert-info">
              <strong>Meeting Announcement</strong>
              <ul class="list-unstyled">
                <li><strong>Quarter:</strong> {{ $meeting->quarter }}</li>
                <li><strong>Year:</strong> {{ $meeting->year }}</li>
                <li><strong>Submission Start:</strong> {{ $meeting->submission_start }}</li>
                <li><strong>Submission End:</strong> {{ $meeting->submission_end }}</li>
                <li><strong>Meeting Date & Time:</strong> {{ $meeting->meeting_date_time }}</li>
                <li><strong>Meeting Type:</strong> {{ $meeting->meeting_type }}</li>
                <li><strong>Status:</strong> 
                  @if($meeting->status == 'scheduled')
                    <span class="badge bg-success">Scheduled</span>
                  @else
                    <span class="badge bg-danger">Not Scheduled</span>
                  @endif
                </li>
              </ul>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <div class="card mt-3">
        <div class="card-body">
          <div class="alert alert-info">
            <p>No meetings available at the moment.</p>
          </div>
        </div>
      </div>
    @endif -->
  </div>
</div>
@endsection
