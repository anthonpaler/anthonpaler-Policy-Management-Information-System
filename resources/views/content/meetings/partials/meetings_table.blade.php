@php 
    $actionColors = [ 'secondary', 'primary', 'success', 'warning', 'info', 'danger']; 
@endphp  
    @if ($meetings->isEmpty())
        <td valign="top" colspan="10" class="dataTables_empty">
            No data available in table
        </td>
    @else
        @foreach($meetings as $index => $meeting)
            <tr>
                <td  class="">{{ $loop->iteration }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        @if(session('isProponent'))
                            @if( $meeting->getMeetingCouncilType() == 0)
                                @if ($meeting->getIsSubmissionClosedAttribute() || $meeting->status == 1)
                                    <a class="action-btn danger active">
                                        <i class='bx bx-lock' ></i>
                                        <span class="tooltiptext">Submission Closed</span>
                                    </a>
                                @else
                                    <a class="action-btn success"  href="{{ route(getUserRole().'.meetings.submit-proposal', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}">
                                        <i class='bx bx-send'></i>
                                        <span class="tooltiptext">Submit Proposal</span>
                                    </a>
                                @endif
                            @endif
                        @endif
                        @if(session('isSecretary'))
                            @php
                                $role = session('user_role');  
                                $campus_id = session('campus_id');

                                $level = match ($role) {
                                    3, 0 , 1 , 2 , 6 => 0,
                                    4 => 1,
                                    5 => 2,
                                    default => 0,
                                };
                            @endphp
                            @if ($level == $meeting->getMeetingCouncilType())
                                @if ($meeting->status == 1)
                                    <a class="action-btn danger active">
                                        <i class='bx bx-lock' ></i>
                                        <span class="tooltiptext">Meeting Closed</span>
                                    </a>
                                @else
                                    @if(!$meeting->has_order_of_business)
                                        @if($meeting->meeting_date_time) 
                                            <a class="action-btn warning" href="{{ route(getUserRole().'.order_of_business.view-generate', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}">
                                                <i class='bx bx-up-arrow-circle'></i> 
                                                <span class="tooltiptext">Generate OOB</span>
                                            </a>
                                        @else
                                            <a class="action-btn warning" onclick="showCantGenerateOOBWarning()">
                                                <i class='bx bx-up-arrow-circle'></i> 
                                                <span class="tooltiptext">Generate OOB</span>
                                            </a>
                                        @endif
                                    @else
                                        <a class="action-btn danger" onclick="showhasOOBWarning()">
                                            <i class='bx bx-up-arrow-circle'></i> 
                                            <span class="tooltiptext">Generate OOB</span>
                                        </a>
                                    @endif
                                @endif
                                <a class="action-btn success"   href="{{ route(getUserRole().'.meeting.edit_meeting', ['level' => $meeting->getMeetingLevel(), 'meeting_id' => Crypt::encrypt($meeting->id)])}}">
                                    <i class='bx bx-edit'></i>
                                    <span class="tooltiptext">Edit</span>
                                </a>
                            @else
                                @if ($level+1  == $meeting->getMeetingCouncilType())
                                    @if ($meeting->getIsSubmissionClosedAttribute() || $meeting->status == 1)
                                        <a class="action-btn danger active">
                                            <i class='bx bx-lock' ></i>
                                            <span class="tooltiptext">Submission Closed</span>
                                        </a>
                                    @else
                                        <a class="action-btn success"   href="{{ route(getUserRole().'.submit.proposal.secretary', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}">
                                            <i class='bx bx-send'></i>
                                            <span class="tooltiptext">Submit Proposal</span>
                                        </a>
                                    @endif
                                @endif
                            @endif
                        @endif
                        <a class="action-btn primary"  href="{{ route(getUserRole().'.meetings.details', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}">
                            <i class="fa-regular fa-eye" style="font-size: .9em;"></i>
                            <span class="tooltiptext">View Meeting Details</span>
                        </a>
                    </div>
                </td>
                <td>{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }}</td>
                <td>{{ $meeting->year }}</td>
                <td>
                    <div style="min-width: 200px">
                        <span class="mb-0 align-items-center d-flex w-100 text-wrap gap-2">
                            <i class='bx bx-radio-circle-marked text-{{ $actionColors[$meeting->council_type] ?? 'primary' }}'></i>
                                @if ($meeting->getMeetingCouncilType() == 0)
                                    {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                                @elseif ($meeting->getMeetingCouncilType() == 1)
                                    {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                                @elseif ($meeting->getMeetingCouncilType() == 2)
                                    {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
                                @endif 
                        </span>
                    </div>
                </td>
                <td>{{ $meeting->getCampusName() }}</td>
                <td>
                    <div class="d-flex flex-column gap-1">
                        <span class="">Start: {{ \Carbon\Carbon::parse($meeting->submission_start)->format('F d, Y') }}</span>
                        <span class="text-danger">End: {{ \Carbon\Carbon::parse($meeting->submission_end)->format('F d, Y') }}</span>
                    </div>
                </td>
                <td>
                    <span>
                        {{ $meeting->meeting_date_time ? \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, h:i A') : 'Not yet set' }}
                    </span>  
                </td>
                @if(session('isProponent'))
                    <td>
                        <a href="{{ session('isProponent') ? route(getUserRole().'.meetings.myProposals', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) : '#' }}" class="text-primary">
                            <span>
                                <i class='bx bx-file-blank' ></i>
                                {{ $meeting->proposals_count }} Proposals
                            </span>
                        </a>
                    </td>
                @endif
                <td>
                    <div class="d-flex align-items-center gap-1 text-{{$meeting->has_order_of_business  ? 'primary' : 'danger'}}">
                        {!! $meeting->has_order_of_business  ?  "<i class='bx bx-like' ></i> Yes"  : "<i class='bx bx-dislike' ></i> No" !!}
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-1 text-{{$meeting->status == 0 ? 'primary' : 'danger'}}">
                        {!! $meeting->status == 0 ? "<i class='bx bx-lock-open'></i>" : "<i class='bx bx-lock' ></i>" !!}
                        {{ config('meetings.status.'.$meeting->status) }}
                    </div>

                </td>
                <!-- <td>
                    <div class="d-flex align-items-center gap-2">
                        @if(session('isProponent'))
                            @if ($meeting->getMeetingCouncilType() == 0)
                                @if ($meeting->getIsSubmissionClosedAttribute() || $meeting->status == 1)
                                    <a class="btn btn-sm btn-danger d-flex gap-2 disabled">
                                        <i class='bx bx-lock'></i>CLOSED
                                    </a>
                                @else
                                    <a class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                        id="submitProposal"
                                        data-meetingStatus="{{ $meeting->status }}"
                                        href="{{ route(getUserRole().'.meetings.submit-proposal', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}"
                                    >
                                        <i class='bx bx-send'></i> SUBMIT
                                    </a>
                                @endif
                            @endif
                            
                            <a class="btn btn-sm btn-success d-flex align-items-center gap-1"
                                data-meetingStatus="{{ $meeting->status }}"
                                href="{{ route(getUserRole().'.meetings.details', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}"
                            >
                                <i class='bx bx-right-top-arrow-circle'></i>VIEW
                            </a>
                        @endif
                        
                        @if(session('isSecretary'))
                            @php
                                $role = session('user_role');  
                                $campus_id = session('campus_id');

                                $level = match ($role) {
                                    3, 0 , 1 , 2 , 6 => 0,
                                    4 => 1,
                                    5 => 2,
                                    default => 0,
                                };
                            @endphp

                            @if ($level == $meeting->getMeetingCouncilType())
                                <a class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                    href="{{ route(getUserRole().'.meetings.details', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}" 
                                >
                                    <i class='bx bx-right-top-arrow-circle'></i>VIEW
                                </a>

                                <a class="btn btn-sm btn-success d-flex align-items-center gap-1"
                                    href="{{ route(getUserRole().'.meeting.edit_meeting', ['level' => $meeting->getMeetingLevel(), 'meeting_id' => Crypt::encrypt($meeting->id)])}}"
                                >
                                    <i class='bx bx-edit'></i> EDIT
                                </a>

                                @if ($meeting->status == 1)
                                    <a class="btn btn-sm btn-danger d-flex gap-2 disabled">
                                        <i class='bx bx-lock'></i> CLOSED
                                    </a>
                                @else
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">

                                        @if(!$meeting->has_order_of_business)
                                            @if($meeting->meeting_date_time) 
                                                <a class="dropdown-item" href="{{ route(getUserRole().'.order_of_business.view-generate', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}">
                                                    <i class='bx bx-up-arrow-circle me-1'></i> Generate OOB
                                                </a>
                                            @else
                                                <a class="dropdown-item text-danger" href="#" onclick="showToastrWarning()">
                                                    <i class='bx bx-up-arrow-circle me-1'></i> Generate OOB
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            @else
                                <a class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                    href="{{ route(getUserRole().'.meetings.details', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}" 
                                >
                                    <i class='bx bx-right-top-arrow-circle'></i>VIEW
                                </a>
                                @if ($level+1  == $meeting->getMeetingCouncilType())
                                    <a class="btn btn-sm btn-success d-flex align-items-center gap-1"
                                        id="submitProposal"
                                        data-meetingStatus="{{ $meeting->status }}"
                                        href="{{ route(getUserRole().'.submit.proposal.secretary', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}"
                                    >
                                        <i class='bx bx-send'></i> SUBMIT
                                    </a>
                                @endif
                            @endif
                        @endif
                    </div>
                </td> -->
            </tr>
        @endforeach
    @endif