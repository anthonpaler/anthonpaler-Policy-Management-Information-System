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
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
 
<script>
 document.addEventListener("DOMContentLoaded", function () {
    var proposalStatus = @json($latestProposal->status ?? null);
    console.log("Latest Proposal Status:", proposalStatus);  // Debugging

    var lottieFile = '';
    var animationContainer = '';

    if (proposalStatus === 2) {
        lottieFile = "/assets/lottie/returnedanimation.json";
        animationContainer = "returnedAnimation";
    } else if (proposalStatus === 1) {
        lottieFile = "/assets/lottie/posted.json";
        animationContainer = "endorsedAnimation";
    } else if (proposalStatus === 0 || proposalStatus === null) {
        lottieFile = "/assets/lottie/Forwait.json";
        animationContainer = "forWaitingAnimation";
    }else if (proposalStatus === 3 || proposalStatus === null) {
        lottieFile = "/assets/lottie/approved.json";
        animationContainer = "approvedAnimation";
    }else if (proposalStatus === 4) {
        lottieFile = "/assets/lottie/posted.json";
        animationContainer = "endorsedAnimation";
    }  else if (proposalStatus === 8 || proposalStatus === null) {
        lottieFile = "/assets/lottie/review.json";
        animationContainer = "reviewAnimation";
    }else if (proposalStatus === 9 || proposalStatus === null) {
        lottieFile = "/assets/lottie/resubmitt.json";
        animationContainer = "resubmittedAnimation";
    }else if (proposalStatus === 10 || proposalStatus === null) {
        lottieFile = "/assets/lottie/confirmed.json";
        animationContainer = "confirmedAnimation";
    }
    
    

    console.log("Lottie file:", lottieFile, "Animation container:", animationContainer); // Debugging

    if (animationContainer) {
        // Show only the selected animation container
        document.getElementById(animationContainer).style.display = 'block';
    }

    if (lottieFile && animationContainer) {
        var containerElement = document.getElementById(animationContainer);
        
        if (containerElement) {
            lottie.loadAnimation({
                container: containerElement,
                renderer: "svg",
                loop: true,
                autoplay: true,
                path: lottieFile
            });
        } else {
            console.error("Lottie container not found:", animationContainer);
        }
    }
});

</script>
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
    <p class="d-flex align-items-center gap-3 text-bold-500" style="font-size: 30px;">
      <i class="bx bxs-megaphone text-danger"></i> ANNOUNCEMENTS <i class="text-danger bx bxs-megaphone"></i>
    </p>
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

    @php
    $userRole = auth()->user()->role;
    $meeting = $meetings->first(); // Get the first meeting or set to null
@endphp

<div class="row">
  <!-- Left Side: Meetings Card -->
  <div class="col-md-6">
    <div class="card shadow-lg h-100 d-flex flex-column">
      <div class="card-header bg-white">
        <h2 class="text-danger font-weight-bold" style="font-size: 30px;">
          MEETINGS
        </h2>
      </div>
      <div class="card-body">
        @if(!empty($meeting))
        @if(auth()->user()->role >= 0 && auth()->user()->role <= 3) 
            @if(
                auth()->user()->role == 3 ||  
                auth()->user()->role == 2 ||  
                (isset($meeting->council_type) && $meeting->council_type == 1 && in_array(auth()->user()->role, [0, 1, 2])) || 
                (isset($meeting->council_type) && $meeting->council_type == 2 && auth()->user()->role == 0) || 
                (isset($meeting->council_type) && $meeting->council_type == 3 && auth()->user()->role == 1)
            )
                <h5 class="text-uppercase font-weight-bold mt-4">FOR MEETING DETAILS</h5>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold text-uppercase text-muted w-25">Quarter</td>
                                <td>{{ config('meetings.quarterly_meetings.' . ($meeting->quarter ?? 'N/A')) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-uppercase text-muted">Year</td>
                                <td>{{ $meeting->year ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-uppercase text-muted">Meeting Date & Time</td>
                                <td>{{ isset($meeting->meeting_date_time) ? \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y - h:i A') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-uppercase text-muted">Meeting Type</td>
                                <td>
                                    <span class="text-primary">
                                        @if ($meeting->level == 0)
                                            {{ config('meetings.council_types.local_level.' . ($meeting->council_type ?? 'N/A')) }}
                                        @elseif ($meeting->level == 1)
                                            {{ config('meetings.council_types.university_level.' . ($meeting->council_type ?? 'N/A')) }}
                                        @else
                                            {{ config('meetings.council_types.board_level.' . ($meeting->council_type ?? 'N/A')) }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-uppercase text-muted">Status</td>
                                <td>
                                    <span class="text-{{ ($meeting->status ?? 0) == 0 ? 'success' : 'warning' }}">
                                        {{ ($meeting->status ?? 0) == 0 ? 'Active' : 'Closed' }}
                                    </span>
                                </td>                    
                            </tr>
                            @if(!empty($meeting->link))
                            <tr>
                                <td class="font-weight-bold text-uppercase text-muted">Online Meeting Link</td>
                                <td>
                                    <a href="{{ $meeting->link }}" target="_blank" rel="noopener noreferrer">
                                        {{ $meeting->link }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route(getUserRole().'.meetings.details', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}" 
                        class="custom-button">
                        See Meeting Details
                     </a>                     
                                         
                </div>
            @else
                <div class="alert alert-warning mt-3">
                    <strong>No Latest Meeting Announcements at the Moment.</strong>    
                </div>
            @endif
        @endif
    @else
        <div class="alert alert-warning mt-3">
            <strong>No meeting has been created yet.</strong>    
        </div>
    @endif    
      </div>
    </div>
  </div>
  @if(session('isSecretary')) 
    <!-- Local Secretary: Show Proposal Summary -->
    <div class="col-md-6">
      <div class="card shadow-lg">
        <div class="card-header bg-white">
          <h2 class="text-danger font-weight-bold" style="font-size: 30px;">
            PROPOSALS
          </h2>
        </div>
        <div class="card-body text-center">
          <span class="badge bg-primary rounded-pill" style="font-size: 2rem; padding: 15px 25px;">
            {{ $proposalsCount }}
          </span>
          <p class="text-muted mt-3" style="font-size: 1.25rem; font-weight: 500;">Total Proposals Submitted</p>
          {{-- <div class="d-flex justify-content-end mt-3">
            <a href="{{ route(getUserRole().'.proposals') }}" class="btn btn-info">
                See All Proposals
            </a>
        </div>         --}}
        </div>
        
      </div>
    </div>
  @endif
  @if(session('isProponent'))
  <!-- Right Side: Advisory Card -->
  <div class="col-md-6">
    <div class="card shadow-lg h-100 d-flex flex-column">
        <div class="card-header bg-white">
            <h2 class="text-danger font-weight-bold" style="font-size: 30px;">
                ADVISORY
            </h2>
        </div>
        <div class="card-body">
            <h5 class="text-uppercase font-weight-bold mt-4">Latest Proposal Status</h5>

            @php
            $statusText = '';
            $badgeClass = '';
            $animationId = '';
            $lottieFile = ''; 
        
            if ($latestProposal) {
                if ($latestProposal->status == 0) {
                    $statusText = 'For Endorsement (Pending Secretary Update)';
                    $badgeClass = 'info';
                    $animationId = 'forWaitingAnimation';
                    $lottieFile = asset('assets/lottie/Forwait.json');
                } elseif ($latestProposal->status == 2) {
                    $statusText = 'Proposal Returned';
                    $badgeClass = 'danger';
                    $animationId = 'returnedAnimation';
                    $lottieFile = asset('assets/lottie/returnedanimation.json');
                } elseif ($latestProposal->status == 1) { 
                    // ✅ Posted to Agenda
                    // $statusText = 'PROPOSALS POST TO AGENDA';
                    $badgeClass = 'success';
                    $animationId = 'endorsedAnimation';
                    $lottieFile = asset('assets/lottie/posted.json');
                } elseif ($latestProposal->status == 3) { 
                    // ✅ Approved status (Change text)
                    $statusText = 'Proposal Approved to the Local Secretary';
                    $badgeClass = 'success';
                    $animationId = 'approvedAnimation';
                    $lottieFile = asset('assets/lottie/approved.json');
                }elseif ($latestProposal->status == 4) { 
                    // ✅ ENDORESED status
                    $statusText = 'Proposal Endorsed to the University Secretary';
                    $badgeClass = 'success';
                    $animationId = 'endorsedAnimation';
                    $lottieFile = asset('assets/lottie/posted.json');
                }elseif ($latestProposal->status == 8) { 
                    // ✅ ENDORESED status
                    $statusText = 'Proposal For Review';
                    $badgeClass = 'info';
                    $animationId = 'reviewAnimation';
                    $lottieFile = asset('assets/lottie/review.json');
                }elseif ($latestProposal->status == 9) { 
                    // ✅ ENDORESED status
                    $statusText = 'Resubmit your Proposal';
                    $badgeClass = 'warning';
                    $animationId = 'resubmittedAnimation';
                    $lottieFile = asset('assets/lottie/resubmitt.json');
                }elseif ($latestProposal->status == 10) { 
                    // ✅ ENDORESED status
                    $statusText = 'Your Proposal is Confirmed by the Secretary';
                    $badgeClass = 'success';
                    $animationId = 'confirmedAnimation';
                    $lottieFile = asset('assets/lottie/confirmed.json');
                }

            }
        @endphp
        

            @if ($userProposalCount > 0)
                <span class="badge bg-label-{{ $badgeClass }} me-1" style="text-transform: none;">
                    {{ $statusText }}
                </span> 
            @endif

            @if ($userProposalCount > 0)
            <ul class="list-group mt-3">
                @if ($returnedProposalCount > 0)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Returned Proposals</span>
                            <span class="badge bg-danger rounded-pill">{{ $returnedProposalCount }}</span>
                        </div>
                    </li>
                @endif
        
                {{-- ✅ Show "Posted to Agenda" ONLY BEFORE the Meeting --}}
                @if ($latestProposal && $latestProposal->status == 1)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Posted to Agenda</span>
                            <span class="badge bg-success rounded-pill">{{ $postedToAgendaCount }}</span>
                        </div>
                    </li>
                @endif
        
                {{-- ✅ Show "Endorsed to University" AFTER the Meeting (Status 4) --}}
                @if ($latestProposal && $latestProposal->status == 5)
                    {{-- <li class="list-group-item"> --}}
                        <div class="card-body text-center">
                            {{-- <span>Approved with Coletilla</span>
                            <span class="badge bg-warning rounded-pill">{{ $coletillaCount }}</span> --}}
                            <p class="text-muted mt-3" style="font-size: 1.25rem; font-weight: 500;">Approved with Coletilla</p>
                            <span class="badge bg-warning rounded-pill" style="font-size: 2rem; padding: 15px 25px;">
                                {{ $coletillaCount  }}
                              </span>
                        </div>
                    {{-- </li> --}}
                @endif
                @if ($latestProposal && $latestProposal->status == 6)
                {{-- <li class="list-group-item"> --}}
                    <div class="card-body text-center">
                        {{-- <span>Endorsed with Coletilla</span>
                        <span class="badge bg-warning rounded-pill">{{  $endorseColletillaCount }}</span> --}}
                        <p class="text-muted mt-3" style="font-size: 1.25rem; font-weight: 500;">Endorsed with Coletilla</p>
                            <span class="badge bg-warning rounded-pill" style="font-size: 2rem; padding: 15px 25px;">
                                {{  $endorseColletillaCount  }}
                              </span>
                    </div>
                {{-- </li> --}}
            @endif
    
                {{-- @if ($deferredProposalCount > 0)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Deferred Proposals</span>
                            <span class="badge bg-warning rounded-pill">{{ $deferredProposalCount }}</span>
                        </div>
                    </li>
                @endif --}}
                @if ($latestProposal && $latestProposal->status == 7)
                {{-- <li class="list-group-item"> --}}
                    <div class="card-body text-center">
                        {{-- <span>Endorsed with Coletilla</span>
                        <span class="badge bg-warning rounded-pill">{{  $endorseColletillaCount }}</span> --}}
                        <p class="text-muted mt-3" style="font-size: 1.25rem; font-weight: 500;">Deferred Proposal</p>
                            <span class="badge bg-warning rounded-pill" style="font-size: 2rem; padding: 15px 25px;">
                                {{  $deferredProposalCount  }}
                              </span>
                    </div>
                    @endif
            </ul>
        @endif        

            @if ($animationId && $lottieFile)
                <div class="d-flex justify-content-center mt-3">
                    <a href="{{ route(getUserRole().'.proposals') }}" class="menu-link">
                        <div id="forWaitingAnimation" style="width: 255px; height: 255px; display: none;"></div>
                        <div id="returnedAnimation" style="width: 255px; height: 255px; display: none;"></div>
                        <div id="endorsedAnimation" style="width: 255px; height: 255px; display: none;"></div>
                        <div id="approvedAnimation" style="width: 255px; height: 255px; display: none;"></div>
                        <div id="endorsedAnimation" style="width: 255px; height: 255px; display: none;"></div>
                        <div id="reviewAnimation" style="width: 255px; height: 255px; display: none;"></div>
                        <div id="resubmittedAnimation" style="width: 255px; height: 255px; display: none;"></div>
                        <div id="confirmedAnimation" style="width: 255px; height: 255px; display: none;"></div>
                    </a>
                </div>
            @endif

            @if ($userProposalCount == 0)
                <div class="alert alert-warning mt-3">
                    <strong>No Proposal Submitted</strong>
                    <p>You have not submitted any proposals yet. Please submit your proposal to proceed.</p>
                </div>
            @endif

        </div>
    </div>
  </div>
@endif
@endsection
