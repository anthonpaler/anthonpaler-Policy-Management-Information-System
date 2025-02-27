@if ($meetings->isEmpty())
    <tr>
        <td colspan="10">
            <div class="alert alert-warning mt-3" role="alert">
                <i class="bx bx-info-circle"></i> There is no meetings at the moment.
            </div>
        </td>
    </tr>
@endif
@foreach($meetings as $meeting)
<tr>
    <td>{{ $loop->iteration }}</td>
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
        <span class="badge bg-label-{{ config('settings.colors.council_types.'.$meeting->council_type) }} me-1" style="text-transform: none;">
            @if ($meeting->level == 1)
                {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
            @else
                @if (isset($university) && $university == true)
                    {{ config('meetings.council_types.university_level.'.$meeting->council_type) }}
                @else 
                    {{ config('meetings.council_types.local_level.'.$meeting->council_type) }}
                @endif
            @endif
        </span>
    </td>
    <td><span class="badge bg-label-warning me-1" style="text-transform: none;">{{ $meeting->proposals_count }} Proposals</span></td>
    <td>
        <a href="{{ route(getUserRole().'.meetings.proposals', ['meeting_id' => encrypt($meeting->id)]) }}" class="btn btn-sm btn-primary d-flex gap-2"><i class="fa-regular fa-eye"></i>View Proposals</a>
    </td>
</tr>
@endforeach