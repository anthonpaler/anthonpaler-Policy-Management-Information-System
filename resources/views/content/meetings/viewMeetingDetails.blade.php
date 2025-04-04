@extends('layouts/contentNavbarLayout')

@section('title', 'Meeting Information')

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
    <a href="#">Meetings Information</a>
</div>

<!-- Meeting Information Layout -->
<div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-content fade-bg-wrapper">
          <div class="fade-bg-con">
            <img src="{{asset('assets/img/backgrounds/slsu_bg_2.jpeg') }}"  class="img-fluid rounded-top user-timeline-image" alt="user timeline image">
          </div>
          <div class="meeting-head-text">
            <div class="d-flex justify-content-between gap-2">
              <h4 class="">{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }} {{ config("meetings.council_types." . ['local_level', 'university_level', 'board_level'][$meeting->getMeetingCouncilType()] . ".{$meeting->council_type}") }}
              {{$meeting->year}}</h4>
              <div class="">
                  <span class="btn btn-sm btn-{{$meeting->status == 0 ? 'primary' : "danger" }} d-flex gap-1">
                    {!! $meeting->status == 0 ? "<i class='bx bxs-lock-open-alt' ></i>" : "<i class='bx bxs-lock-alt' ></i>" !!}
                    {{ config('meetings.status.'.$meeting->status) }}
                  </span>
              </div>
            </div>
            <p>
              @if(!empty($meeting) && !empty($meeting->description))
                  {{ $meeting->description }}
              @else
                  <span class="text-muted">No Description Available</span>
              @endif
            </p>
          </div>
          <div class="p-4">

          </div>
        </div>
      </div>
    </div>
</div>
<div class="row">
  <div class="col mb-4">
    <div class="card overflow-auto" style="max-width: 100%; white-space: nowrap;">
      <div class="card-body">
        <div class="card-header p-0">
          <small class="text-muted">MEETING INFORMATION</small>
          <hr>
          <div class="d-flex flex-column gap-3">
            <div class="d-flex flex-nowrap gap-3 align-items-center">
              <i class='bx bx-help-circle'></i>
              <strong class="text-nowrap">Status : </strong>
              <span class="text-nowrap">{{$meeting->status == 0 ? 'Active' : "Closed" }}</span>
            </div>
            <div class="d-flex flex-nowrap gap-3 align-items-center">
              <i class='bx bx-box'></i>
              <strong class="text-nowrap">Council Type : </strong>
              <span class="text-nowrap">
                {{ config("meetings.council_types." . ['local_level', 'university_level', 'board_level'][$meeting->getMeetingCouncilType()] . ".{$meeting->council_type}") }}
              </span>
            </div>
            <div class="d-flex flex-nowrap gap-3 align-items-center">
              <i class='bx bx-user-voice' ></i>
              <strong class="text-nowrap">Modality : </strong>
              <span class="text-nowrap">{{ config('meetings.modalities.' . ($meeting->modality ?? 'No Modality yet'), 'No Modality yet') }}</span>
            </div>
            @if ($meeting->modality == 1 || $meeting->modality == 3)
              <div class="d-flex flex-nowrap gap-3 align-items-center">
                <i class='bx bx-building-house' ></i>
                <strong class="text-nowrap">Venue : </strong>
               <span class="text-nowrap"> {{ $meeting->venue ?? 'Not Set' }} </span>
              </div>
            @elseif ($meeting->modality == 2 || $meeting->modality == 3)
              <div class="d-flex flex-nowrap gap-3 align-items-center">
                <i class='bx bxs-devices'></i>
                <strong class="text-nowrap">Platform : </strong>
                <span class="text-nowrap">{{ config('meetings.mode_if_online_types.'.$meeting->mode_if_online) }} - Online</span>
              </div>
              <div class="d-flex flex-nowrap gap-3 align-items-center">
                <i class='bx bx-link' ></i>
                <strong class="text-nowrap">Link : </strong>
                @if ($meeting->link)
                
                @endif
                <a href="{{$meeting->link}}"  class="text-primary m-0"><i class='bx bx-link me-1'></i>Cick Here</a>
              </div>
            @else
                <span class="form-label m-0">Venue or platform not yet set</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card overflow-auto" style="max-width: 100%; white-space: nowrap;">
      <div class="card-body">
        <div class="card-header p-0">
          <small class="text-muted">MEETING SCHEDULES AND PERIOD</small>
          <hr>
          <div class="d-flex flex-column gap-3">
            <div class="d-flex flex-nowrap gap-3 align-items-center">
              <i class='bx bx-calendar-star' ></i>
              <strong class="text-nowrap">Year : </strong>
              <span class="text-nowrap">{{ $meeting->year }}</span>
            </div>
            <div class="d-flex flex-nowrap gap-3 align-items-center">
              <i class='bx bx-calendar-event' ></i>
              <strong class="text-nowrap">Quarter : </strong>
              <span class="text-nowrap">{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }} </span>
            </div>
            <div class="d-flex flex-nowrap gap-3 align-items-center">
              <i class='bx bx-calendar' ></i>
              <strong class="text-nowrap">Submission Start : </strong>
              <span class="text-nowrap"> {{ \Carbon\Carbon::parse($meeting->submission_start)->format('F d, Y g:i A') }}</span>
            </div>
            <div class="d-flex flex-nowrap gap-3 align-items-center">
              <i class='bx bx-calendar' ></i>
              <strong class="text-nowrap">Submission End : </strong>
              <span class="text-nowrap"> {{ \Carbon\Carbon::parse($meeting->submission_end)->format('F d, Y g:i A') }}</span>
            </div>
            <div class="d-flex flex-nowrap gap-3 align-items-center">
              <i class='bx bx-calendar'></i>
              <strong class="text-nowrap">Meeting Date and Time: </strong>

              @if(!empty($meeting) && !empty($meeting->meeting_date_time))
                  <span class="text-nowrap">
                      {{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y g:i A') }}
                  </span>
              @else
                  <span class="text-danger">Not Yet Set</span>
              @endif
            </div>
          </div>  
        </div>
      </div>
    </div>
  </div>
</div> 
 <!-- End Meeting Information Layout   -->
@endsection
