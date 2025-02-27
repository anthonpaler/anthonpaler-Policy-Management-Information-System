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
        <td  class="p-4">{{ $loop->index +1 }}</td>
        @if (in_array(auth()->user()->role, [3, 4, 5]))
            <td>
                {{ config('meetings.level.'.$meeting->level) }}
            </td>
        @endif
        <td>{{ config('meetings.quaterly_meetings.'.$meeting->quarter) }}</td>
        <td>{{ $meeting->year }}</td>
        <td>
            <span class="badge bg-label-{{$meeting->status == 0? 'success' : 'danger'}} me-1" style="text-transform: none;">
                {{ config('meetings.status.'.$meeting->status) }}
            </span>
        </td>
        <td>
            @php
                $hasOrderOfBusiness = \App\Models\OrderOfBusiness::where('meeting_id', $meeting->id)->exists() ? true : false
            @endphp
            <span class="badge bg-label-{{ $hasOrderOfBusiness ? 'success' : 'danger' }} me-1" style="text-transform: none;">
                {{ $hasOrderOfBusiness ? 'Yes' : 'No' }}
            </span>
        </td>
        <td>
            <span class="badge bg-label-{{ $actionColors[$meeting->council_type] ?? 'primary' }} me-1" style="text-transform: none;">
                @if ($meeting->level == 0)
                    {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                @elseif ($meeting->level == 1)
                    {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                @else
                    {{ config('meetings.council_types.board_level.'.$meeting->council_type) }}
                @endif
            </span>
        </td>
        <td>
            <div class="d-flex flex-column gap-1">
                <span class=""><strong>Date Start: </strong>{{ \Carbon\Carbon::parse($meeting->submission_start)->format('F d, Y') }}</span>
                <span class=""><strong>Date End: </strong> {{ \Carbon\Carbon::parse($meeting->submission_end)->format('F d, Y') }}</span>
            </div>
        </td>
        <td>
            <strong>
                {{ $meeting->meeting_date_time ? \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F d, Y, h:i A') : 'Not yet set' }}
            </strong>
        </td>
        @if(in_array(auth()->user()->role, [0,1,2]))
            <td>
                <a href="{{ route(getUserRole().'.meetings.proposals', ['meeting_id' => encrypt($meeting->id)]) }}" class="btn btn-sm btn-info d-flex gap-2">
                    <i class='bx bx-file-blank' ></i>
                    {{ $meeting->proposals_count }}  Proposals
                </a>
            </td>
        @endif
        <td>
            @php
                $currentDate = now();
                $submissionEnd = \Carbon\Carbon::parse($meeting->submission_end);
                $meetingDate = \Carbon\Carbon::parse($meeting->datetime);
                $submissionStart= \Carbon\Carbon::parse($meeting->submission_start);
                $isSubmissionClosed = $currentDate->greaterThan($submissionEnd) || $currentDate->lessThan($submissionStart) ||  $currentDate->greaterThan($meetingDate);
            @endphp
             @if (in_array(auth()->user()->role, [0,1,2,6]))
                <div class="d-flex align-items-center gap-2">
                    @if ($isSubmissionClosed || $meeting->status == 1)
                        <a class="btn btn-sm btn-danger d-flex gap-2 disabled">
                            <i class='bx bx-lock'></i> Submission Closed
                        </a>
                    @else
                        <a class="btn btn-sm btn-primary d-flex gap-2"
                            id="submitProposal"
                            data-meetingStatus="{{ $meeting->status }}"
                            href="{{ route(getUserRole().'.meetings.submit-proposal', ['meeting_id'=> encrypt($meeting->id)]) }}"
                            data-hasOrderOfBusiness=""
                        >
                            <i class='bx bx-send'></i> Submit Proposal
                        </a>
                    @endif
                    <a class="btn btn-sm btn-success d-flex gap-2"
                        href="{{ route('meetings.details', ['meeting_id'=> encrypt($meeting->id)]) }}"
                    >
                        <i class="fa-regular fa-eye"></i> View Meeting
                    </a>
                </div>
            @else
                @if ($meeting->status == 1)
                    <div class="d-flex align-items-center gap-2">
                        <a class="btn btn-sm btn-danger d-flex gap-2 disabled">
                            <i class='bx bx-lock'></i> Meeting Closed
                        </a>
                        <a class="btn btn-sm btn-success d-flex gap-2"
                            href="{{ route('meetings.details', ['meeting_id'=> encrypt($meeting->id)]) }}"
                        >
                            <i class="fa-regular fa-eye"></i> View Meeting
                        </a>
                    </div>
                @else
                    @php
                        $user = auth()->user(); 
                        $role = $user->role;  
                        $creatorId = $user->id;
                        $employeeId = $user->employee_id;

                        $campus_id = App\Models\Employee::find($employeeId)?->campus ?? null;

                        $level = match ($role) {
                            3, 6 => 0,
                            4 => 1,
                            5 => 2,
                            default => 0,
                        };
                    @endphp


                    @if (($level == $meeting->level) && ($campus_id == $meeting->campus_id))

                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">

                            @php
                                $hasOrderOfBusiness = \App\Models\OrderOfBusiness::where('meeting_id', $meeting->id)->exists();
                            @endphp

                            @if(!$hasOrderOfBusiness)
                                @if($meeting->datetime) 
                                <a class="dropdown-item" href="{{ route(getUserRole().'.order_of_business.view-generate', ['meeting_id'=> encrypt($meeting->id)]) }}">
                                    <i class='bx bx-up-arrow-circle me-1'></i> Generate OOB
                                </a>
                                @else
                                    <a class="dropdown-item text-danger" href="#" onclick="showToastrWarning()">
                                        <i class='bx bx-up-arrow-circle me-1'></i> Generate OOB
                                    </a>
                                @endif
                            @endif

                            <a class="dropdown-item" href="{{ route(getUserRole().'.meetings.edit', ['meeting_id' => Crypt::encrypt($meeting->id)])}}">
                                <i class="bx bx-edit-alt me-1"></i> Edit Meeting
                            </a>
                            <a class="dropdown-item"
                                href="{{ route('meetings.details', ['meeting_id'=> encrypt($meeting->id)]) }}"
                            >
                                <i class="fa-regular fa-eye me-1"></i> View Meeting
                            </a>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <a class="btn btn-sm btn-primary d-flex gap-2"
                                id="submitProposal"
                                data-meetingStatus="{{ $meeting->status }}"
                                href="{{route(getUserRole().'.submit.proposal.secretary', ['meeting_id' => Crypt::encrypt($meeting->id)])}}"
                            >
                                <i class='bx bx-send'></i> Submit Proposal
                            </a>
                            <a class="btn btn-sm btn-success d-flex gap-2"
                                href="{{ route('meetings.details', ['meeting_id'=> encrypt($meeting->id)]) }}"
                            >
                                <i class="fa-regular fa-eye"></i> View Meeting
                            </a>
                        </div>
                    @endif
                @endif
            @endif
        </td>
    </tr>
    @endforeach
@endif