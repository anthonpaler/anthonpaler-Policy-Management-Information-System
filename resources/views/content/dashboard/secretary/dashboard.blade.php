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
    @if(session('user_role') != 8)
      <p class="font-medium-3 text-bold-500 d-flex align-items-center gap-3"><i class="bx bxs-megaphone text-danger"></i> ANNOUNCEMENTS <i class="text-danger bx bxs-megaphone"></i></p>
      <div class="card mt-3">
        <div class="card-body">
          <p>No announcements have been made yet.</p>
        </div>
        {{-- @foreach ($meetings as $meeting)
          <span>{{$loop->iteration}} HA {{$meeting->id}} HAHAHAH   {{$meeting->getProposalCount()}}</span>
        @endforeach --}}
      </div>
    @endif

  </div>
  
</div>

@if(session('user_role') == 8)
    <div class="row mb-12 g-6">
        <div class="col-md-6 col-lg-4 mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Of Business (Agenda)</h5>
                    <p class="card-text">Please click the button below to view the Order Of Business or the Provisional Agenda</p>

                    @php
                        // Assume you're getting the latest or active BOR-level OOB ID dynamically here
                        $oob = \App\Models\BoardOob::where('status', 1)->latest()->first();
                    @endphp

                    @if($oob)
                        <a href="{{ route(getUserRole().'.order_of_business.view-oob', ['level' => $oob->meeting->getMeetingLevel(), 'oob_id'=> encrypt( $oob->id)]) }}"
                           class="btn btn-primary">VIEW AGENDA</a>
                    @else
                        <button class="btn btn-secondary" disabled>No Agenda Available</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
  
@endsection
