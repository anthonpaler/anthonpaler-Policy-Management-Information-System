@forelse ($orderOfBusiness as $index => $oob)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>
            {{ config('meetings.level.'.$oob->meetings->level) }}
        </td>
        <td>{{ config('meetings.quaterly_meetings.'.$oob->meetings->quarter) ?? 'N/A' }}</td>
        <td>{{ $oob->meetings->year }}</td>
        <td>
            {{ config('meetings.quaterly_meetings.'.$oob->meetings->quarter) }} 
                
            @if ($oob->meetings->level === 0)
                {{ config("meetings.council_types.local_level.{$oob->meetings->council_type}") }}
            @endif
            @if ($oob->meetings->level === 1)
                {{ config("meetings.council_types.university_level.{$oob->meetings->council_type}") }}
            @endif
            @if ($oob->meetings->level === 2)
                {{ config("meetings.council_types.board_level.{$oob->meetings->council_type}") }}
            @endif
        
        </td>
        <td>
            <span class="badge 
                {{ $oob->status == 0 ? 'bg-label-warning' : 'bg-label-success' }} me-1">
                {{ $oob->status == 0 ? 'Draft' : 'Disseminated' }}
            </span>
        </td>
        <td>{{ \Carbon\Carbon::parse($oob->meetings->meeting_date_time)->format('l, M. d, Y H:i') }}</td>
        <td>
            <a href="{{ route(getUserRole().'.order_of_business.view-oob', ['oob_id'=> encrypt( $oob->id)]) }}" 
                class="btn btn-sm btn-primary d-flex gap-2">
                <i class="fa-regular fa-eye"></i> VIEW DETAILS
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8">
            <div class="alert alert-warning mt-3" role="alert">
                <i class="bx bx-info-circle"></i> No meetings found in the Order of Business.
            </div>
        </td>
    </tr>
@endforelse