@forelse ($orderOfBusiness as $index => $oob)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $oob->meeting->getCampusName() }}</td>
        <td>{{ config('meetings.quarterly_meetings.'.$oob->meeting->quarter) ?? 'N/A' }}</td>
        <td>{{ $oob->meeting->year }}</td>
        <td>
            {{ config('meetings.quarterly_meetings.'.$oob->meeting->quarter) }}

                @if ($oob->meeting->getMeetingCouncilType() === 0)
                    {{ config("meetings.council_types.local_level.{$oob->meeting->council_type}") }}
                @endif
                @if ($oob->meeting->getMeetingCouncilType() === 1)
                    {{ config("meetings.council_types.university_level.{$oob->meeting->council_type}") }}
                @endif
                @if ($oob->meeting->getMeetingCouncilType() === 2)
                    {{ config("meetings.council_types.board_level.{$oob->meeting->council_type}") }}
                @endif
        </td>
        <td>
            <span>
                {{ $oob->meeting->meeting_date_time ? \Carbon\Carbon::parse($oob->meeting->meeting_date_time)->format('F d, Y, h:i A') : 'Not yet set' }}
            </span>
        </td>
        <td>
            <div class="d-flex align-items-center gap-2 text-{{$oob->status == 0 ? 'warning' : 'primary'}}">
                {!! $oob->status == 0 ? "<i class='bx bx-down-arrow-circle' ></i> Draft" : "<i class='bx bx-mail-send' ></i> Disseminated" !!}
            </div>
        </td>
        <td>
            <a href="{{ route(getUserRole().'.order_of_business.view-oob', ['level' => $oob->meeting->getMeetingLevel(), 'oob_id'=> encrypt( $oob->id)]) }}"
            class="btn btn-sm btn-primary d-flex gap-2" style="max-width: 100px;">
                <i class="fa-regular fa-eye"></i> VIEW OOB
            </a>
        </td>
    </tr>
@empty
    <!-- <tr>
        <td colspan="7">
            <div class="alert alert-warning mt-3" role="alert">
                <i class="bx bx-info-circle"></i> No meetings found in the Order of Business.
            </div>
        </td>
    </tr> -->
@endforelse
