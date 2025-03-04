@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Meeting')

@section('content')
<div class="bread-crumbs overflow-auto" style="max-width: 100%; white-space: nowrap;">
    <h5>Dashboard</h5>
    <div class="divider"></div>
    <a href="/">
        <i class='bx bx-home-alt' ></i>
    </a>
    <i class='bx bx-chevron-right' ></i>
    <a href="">Meetings</a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#">Edit Meeting</a>
</div>
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3 border-top pt-3">
    <h5 class="mb-0">Edit Meeting</h5>
    <small class="text-muted float-end">Please fill the details accordingly.</small>
</div>
<form method="POST" action="{{route( getUserRole().'.meetings.save-edit', ['level' => $meeting->getMeetingLevel(), 'meeting_id' => encrypt($meeting->id)])}}"  id="meetingForm">
@csrf
    <div class="row">
        <div class="col mb-4">
            <div class="card" style="height: 100%;">
                <div class="card-body">
                    <small class="text-primary">MEETING INFORMATION</small>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" for="sub_type">Council Type<span class="ms-1 text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                <span id="basic-icon-default-phone2" class="input-group-text">
                                    <i class='bx bx-user-pin'></i>
                                </span>
                                <select class="form-control @error('type') is-invalid @enderror" name="council_type" required>
                                    <option value="">Select Meeting Type</option>
                                    @if (auth()->user()->role == 3)
                                            @foreach (config('meetings.council_types.local_level') as $index => $item)
                                            <option value="{{ $index }}"
                                            {{ (isset($meeting) && (int) $meeting->council_type === (int) $index) ? 'selected' : '' }}>
                                            {{ $item }}
                                        </option>
                                            @endforeach
                                        @endif
                                        @if (auth()->user()->role == 4)
                                            @foreach (config('meetings.council_types.university_level') as $index => $item)
                                            <option value="{{ $index }}"
                                            {{ (isset($meeting) && (int) $meeting->council_type === (int) $index) ? 'selected' : '' }}>
                                            {{ $item }}
                                        </option>
                                            @endforeach
                                        @endif
                                        @if (auth()->user()->role == 5)
                                            @foreach (config('meetings.council_types.board_level') as $index => $item)
                                            <option value="{{ $index }}"
                                            {{ (isset($meeting) && (int) $meeting->council_type === (int) $index) ? 'selected' : '' }}>
                                            {{ $item }}
                                            </option>
                                            @endforeach
                                        @endif
                                </select>
                                @error('type')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" for="status">Status<span class="ms-1 text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span id="" class="input-group-text">
                                        <i class='bx bx-badge-check'></i>
                                    </span>
                                    <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                                        @foreach (config('meetings.status') as $index => $item)
                                            <option value="{{ $index }}"
                                                {{ (isset($meeting) && (int) $meeting->status === (int) $index) ? 'selected' : '' }}>
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="basic-icon-default-message">Description & Other Reminders (Optional)</label>
                        <div class="input-group input-group-merge">
                            <!-- <span id="basic-icon-default-message2" class="input-group-text">
                                <i class="bx bx-comment"></i>
                            </span> -->
                            <textarea
                                id="basic-icon-default-message"
                                class="form-control"
                                placeholder="Enter description..."
                                aria-label="Enter description..."
                                aria-describedby="basic-icon-default-message2"
                                name="description"
                                rows="5"
                            >{{ old('description', isset($meeting) && $meeting->description ? $meeting->description : '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>  
        <div class="col mb-4">
            <div class="card" style="height: 100%;">
                <div class="card-body">
                    <small class="text-primary">SUBMISSION / MEETING DATES</small>
                    <div class="mb-3 mt-3">
                        <label class="form-label" for="title">Quarter <span class="ms-1 text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span id="basic-icon-default-company2" class="input-group-text">
                                <i class='bx bx-border-all'></i>
                            </span>

                            <select class="form-control @error('quarter') is-invalid @enderror" name="quarter" required>
                                <option value="">Select Quarter</option>
                                @foreach (config('meetings.quaterly_meetings') as $index => $item)
                                    <option value="{{ $index }}"
                                        {{ (isset($meeting) && (int) $meeting->quarter === (int) $index) ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                            @error('quarter')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="title">Year <span class="ms-1 text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span id="basic-icon-default-company2" class="input-group-text">
                                <i class='bx bx-calendar-alt'></i>
                            </span>
                            <select class="form-control @error('year') is-invalid @enderror" name="year" required>
                                <option value="">Select Year</option>
                                @for ($year = date('Y'); $year <= date('Y') + 5; $year++)
                                    <option value="{{ $year }}"
                                        {{ (isset($meeting) && (int) $meeting->year === (int) $year) ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('year')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" for="sub_type">Submission Start<span class="ms-1 text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-submission-start" class="input-group-text">
                                        <i class="bx bx-calendar"></i>
                                    </span>
                                    <input
                                        type="date"
                                        id="submission_start"
                                        class="form-control basic-icon-default-company @error('submission_start') is-invalid @enderror"
                                        name="submission_start"
                                        value="{{ old('submission_start', isset($meeting) && $meeting->submission_start ? (new DateTime($meeting->submission_start, new DateTimeZone('Asia/Manila')))->format('Y-m-d') : (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d')) }}"
                                        required
                                    />
                                    @error('submission_start')
                                        <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" for="basic-icon-default-message">Submission End<span class="ms-1 text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-submission-end" class="input-group-text">
                                        <i class="bx bx-calendar"></i>
                                    </span>
                                    <input
                                        type="date"
                                        id="submission_end"
                                        class="form-control basic-icon-default-company @error('submission_end') is-invalid @enderror"
                                        name="submission_end"
                                        value="{{ old('submission_end', isset($meeting) && $meeting->submission_end ? (new DateTime($meeting->submission_end, new DateTimeZone('Asia/Manila')))->format('Y-m-d') : (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d')) }}"
                                        min="{{ date('Y-m-d') }}"
                                        required
                                    />
                                    @error('submission_end')
                                        <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col  mb-4">
            <div class="card">
                <div class="card-body">
                    <small class="text-primary">OTHER MEETING INFORMATION</small>
                    <div class="meeting-info-con mt-3" id="meetingInfo">
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="status">Modality</label>
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-company2" class="input-group-text">
                                            <i class='bx bx-shape-square' ></i>
                                        </span>
                                        <select
                                        id="modality"
                                        class="form-control @error('status') is-invalid @enderror"
                                        name="modality"
                                        >
                                        <option value="">Select Modality</option>
                                            @foreach (config('meetings.modalities') as $index => $item)
                                                <option value="{{ $index }}"
                                                    {{ old('modality', isset($meeting) ? $meeting->modality : '') == $index ? 'selected' : '' }}>
                                                    {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @error('status')
                                        <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                @php
                                    $minMeetingDate = isset($meeting) && $meeting->submission_end 
                                        ? (new DateTime($meeting->submission_end, new DateTimeZone('Asia/Manila')))->modify('+1 day')->format('Y-m-d\TH:i') 
                                        : date('Y-m-d\TH:i');
                                @endphp

                                <div class="mb-3">
                                    <label class="form-label" for="meeting_date_time">Meeting Date & Time</label>
                                    <div class="input-group">
                                        <span id="basic-icon-default-submission-start" class="input-group-text">
                                            <i class="bx bx-calendar"></i>
                                        </span>
                                        <input
                                            type="datetime-local"
                                            id="meeting_date_time"
                                            name="meeting_date_time"
                                            class="form-control @error('meeting_date_time') is-invalid @enderror"
                                            value="{{ isset($meeting) && $meeting->meeting_date_time ? (new DateTime($meeting->meeting_date_time, new DateTimeZone('Asia/Manila')))->format('Y-m-d\TH:i') : '' }}"
                                            min="{{ $minMeetingDate }}"
                                        />
                                        @error('meeting_date_time')
                                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row  {{ $meeting->modality == 2 ||   $meeting->modality == 3 ? '': 'd-none'}}" id="onlineModeInfo">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="platform">Platform</label>
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-company2" class="input-group-text">
                                            <i class="bx bx-buildings"></i>
                                        </span>
                                        <select
                                            id="mode_if_online"
                                            class="form-control basic-icon-default-company  @error('venue') is-invalid @enderror"
                                            name="mode_if_online"

                                        >
                                            <option value="">Select Platform</option>
                                            @foreach (config('meetings.mode_if_online_types') as $index => $item)
                                                <option value="{{ $index }}"
                                                {{ (isset($meeting) && (int) $meeting->mode_if_online === (int) $index) ? 'selected' : '' }}>
                                                {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('mode_if_online_types')
                                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="platform">Link</label>
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-company2" class="input-group-text">
                                            <i class='bx bx-link' ></i>
                                        </span>
                                        <input
                                            id="link"
                                            class="form-control basic-icon-default-company @error('venue') is-invalid @enderror"
                                            name="link"
                                            placeholder="Enter link"
                                            value="{{ old('description', isset($meeting) && $meeting->link ? $meeting->link : '') }}"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 {{ $meeting->modality == 1 ||   $meeting->modality == 3 ? '': 'd-none'}}" id="venueField">
                            <label class="form-label" for="basic-icon-default-company">Venue</label>
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text">
                                    <i class="bx bx-buildings"></i>
                                </span>
                                <select
                                    id="venue"
                                    class="form-control basic-icon-default-company @error('venue') is-invalid @enderror"
                                    name="venue"
                                >
                                    <option value="">Select Venue</option>
                                    @foreach ($venues as $venue)
                                        <option value="{{ $venue->id }}"
                                            {{ (isset($meeting) && $meeting->venue == $venue->id) ? 'selected' : (old('venue') == $venue->id ? 'selected' : "") }}>
                                            {{ $venue->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('venue')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="mt-3 btn btn-primary d-flex gap-2" id="editMeetingBtn">
        <i class='bx bx-save'></i>
        <span>Save Changes</span>
    </button>
</form>
<script src="{{asset('assets/js/meetings.js')}}"></script>

@endsection 
