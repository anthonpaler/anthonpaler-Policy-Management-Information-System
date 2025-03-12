@php 
    $actionColors = [ 'secondary', 'primary', 'success', 'warning', 'info', 'danger']; 
@endphp  
   @if ($meetings->isEmpty())
        <tr>
            <td colspan="10">
                <div class="alert alert-warning mt-3" role="alert">
                    <i class="bx bx-info-circle"></i> There is no meetings at the moment.
                </div>
            </td>
        </tr>
    @else
        @foreach($meetings as $index => $meeting)
            <tr>
                <td  class="">{{ $loop->iteration }}</td>
                @if(session('isSecretary'))
                    <td>
                        {{ config('meetings.level.0') }}
                    </td>
                @endif
                <td>{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }}</td>
                <td>{{ $meeting->year }}</td>
                <td>
                    <div class="d-flex align-items-center gap-1 text-{{$meeting->status == 0 ? 'primary' : 'danger'}}">
                        {!! $meeting->status == 0 ? "<i class='bx bxs-lock-open-alt' ></i>" : "<i class='bx bxs-lock-alt' ></i>" !!}
                        {{ config('meetings.status.'.$meeting->status) }}
                    </div>

                </td>
                <td>
                    <div class="d-flex align-items-center gap-1 text-{{$meeting->has_order_of_business  ? 'primary' : 'danger'}}">
                        {!! $meeting->has_order_of_business  ?  "<i class='bx bxs-like' ></i> Yes"  : "<i class='bx bxs-dislike' ></i> No" !!}
                    </div>
                </td>
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
                <td>
                    <div class="d-flex flex-column gap-1">
                        <span class=""><span class="text-primary">Start: </span>{{ \Carbon\Carbon::parse($meeting->submission_start)->format('F d, Y') }}</span>
                        <span class=""><span class="text-danger">End: </span> {{ \Carbon\Carbon::parse($meeting->submission_end)->format('F d, Y') }}</span>
                    </div>
                </td>
                <td>
                    <span>
                        {{ $meeting->meeting_date_time ? \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, h:i A') : 'Not yet set' }}
                    </span>  
                </td>
                @if(session('isProponent'))
                    <td>
                        <a href="" class="text-primary">
                            <span>
                                <i class='bx bx-file-blank' ></i>
                                {{ $meeting->proposals_count }} Proposals
                            </span>
                        </a>
                    </td>
                @endif
                <td>
                    <div class="d-flex align-items-center gap-2">
                        @if(session('isProponent'))
                            @if ($meeting->getIsSubmissionClosedAttribute() || $meeting->status == 1)
                                <a class="btn btn-sm btn-danger d-flex gap-2 disabled">
                                    <i class='bx bx-lock'></i>Closed
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
                                    3, 6 => 0,
                                    4 => 1,
                                    5 => 2,
                                    default => 0,
                                };
                            @endphp

                            @if ($level == $meeting->getMeetingCouncilType() && ($campus_id == $meeting->campus_id))
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

                                <a class="btn btn-sm btn-success d-flex align-items-center gap-1"
                                    id="submitProposal"
                                    data-meetingStatus="{{ $meeting->status }}"
                                    href="{{ route(getUserRole().'.submit.proposal.secretary', ['level' => $meeting->getMeetingLevel(), 'meeting_id'=> encrypt($meeting->id)]) }}"
                                >
                                    <i class='bx bx-send'></i> SUBMIT
                                </a>
                            @endif
                    
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    @endif