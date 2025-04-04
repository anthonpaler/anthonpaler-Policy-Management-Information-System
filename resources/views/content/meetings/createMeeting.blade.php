@extends('layouts/contentNavbarLayout')

@section('title', 'Create Meeting')

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
    <a href="#">Create Meeting</a>
</div>
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3 border-top pt-3">
    <h5 class="mb-0">Call for Submission</h5>
    <small class="text-muted float-end">Please fill the details accordingly.</small>
</div>
<form method="POST" action="{{route( getUserRole().'.meetings.create')}}" id="meetingForm">
@csrf
    <div class="row">
        <div class="col mb-4">
            <div class="card" style="height: 100%;">
                <div class="card-body">
                    <small class="text-primary">MEETING INFORMATION</small>
                    <div class="mb-3 mt-3">
                        <label class="form-label" for="sub_type">Council Type<span class="ms-1 text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                        <span id="basic-icon-default-phone2" class="input-group-text">
                            <i class='bx bx-user-pin'></i>
                        </span>
                        <select class="form-select @error('type') is-invalid @enderror" name="council_type" required>
                                <option value="">Select Meeting Type</option>
                                @if (auth()->user()->role == 3)
                                    @foreach (config('meetings.council_types.local_level') as $index => $item)
                                        <option value="{{ $index }}" {{ old('type') == $index ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                @endif
                                @if (auth()->user()->role == 4)
                                    @foreach (config('meetings.council_types.university_level') as $index => $item)
                                        <option value="{{ $index }}" {{ old('type') == $index ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                @endif
                                @if (auth()->user()->role == 5)
                                    @foreach (config('meetings.council_types.board_level') as $index => $item)
                                        <option value="{{ $index }}" {{ old('type') == $index ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                @endif
                        </select>
                        @error('type')
                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                        @enderror
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
                            ></textarea>
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
                            <span  class="input-group-text">
                                <i class='bx bx-border-all'></i>
                            </span>

                            <select class="form-select @error('quarter') is-invalid @enderror" name="quarter" required>
                                <option value="">Select Quarter</option>
                                @foreach (config('meetings.quaterly_meetings') as $index => $item)
                                    <option value="{{ $index }}" {{ old('quarter') == $index ? 'selected' : '' }}>
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
                            <span  class="input-group-text">
                                <i class='bx bx-calendar-alt'></i>
                            </span>
                            <select class="form-select @error('year') is-invalid @enderror" name="year" required>
                                <option value="">Select Year</option>
                                @for ($year = date('Y'); $year <= date('Y') + 5; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
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
                                        class="form-control  @error('submission_start') is-invalid @enderror"
                                        name="submission_start"
                                        value="{{ old('submission_start', (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d')) }}"
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
                                        class="form-control  @error('submission_end') is-invalid @enderror"
                                        name="submission_end"
                                        value="{{ old('submission_end', date('Y-m-d')) }}"
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
                    <div class="d-flex gap-3 mb-3 mt-3 align-items-center">
                        <input class="switch" type="checkbox" id="addMeetInfo" name="addMeetInfo" value="1">
                        </label>
                        <span  class="form-label">Add additional meeting information</span>
                    </div>
                    <div class="meeting-info-con d-none" id="meetingInfo">
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="status">Modality</label>
                                    <div class="input-group input-group-merge">
                                        <span  class="input-group-text">
                                            <i class='bx bx-shape-square' ></i>
                                        </span>
                                        <select
                                        id="modality"
                                        class="form-select"
                                        name="modality"
                                        >
                                        <option value="">Select Modality</option>
                                        @foreach (config('meetings.modalities') as $index => $item)
                                            <option value="{{ $index }}" {{ old('modality') == $index ? 'selected' : '' }}>
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
                                <div class="mb-3">
                                    <label class="form-label" for="meeting_date_time">Meeting Date & Time</label>
                                    <div class="input-group ">
                                        <span id="basic-icon-default-submission-start" class="input-group-text">
                                            <i class="bx bx-calendar"></i>
                                        </span>
                                        <input
                                            type="datetime-local"
                                            id="meeting_date_time"
                                            name="meeting_date_time"
                                            class="form-control @error('meeting_date_time') is-invalid @enderror"
                                            value="{{ old('submission_end', date('Y-m-d')) }}"
                                            min="{{ date('Y-m-d') }}"
                                        />
                                        @error('meeting_date_time')
                                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row  d-none" id="onlineModeInfo">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="platform">Platform</label>
                                    <div class="input-group input-group-merge">
                                        <span  class="input-group-text">
                                            <i class="bx bx-buildings"></i>
                                        </span>
                                        <select
                                            id="mode_if_online"
                                            class="form-select   @error('venue') is-invalid @enderror"
                                            name="mode_if_online"

                                        >
                                            <option value="">Select Platform</option>
                                            @foreach (config('meetings.mode_if_online_types') as $index => $item)
                                                <option value="{{ $index }}">{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('modality')
                                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="platform">Link</label>
                                    <div class="input-group input-group-merge">
                                        <span  class="input-group-text">
                                            <i class='bx bx-link' ></i>
                                        </span>
                                        <input
                                            id="mode_if_online"
                                            class="form-control  @error('venue') is-invalid @enderror"
                                            name="link"
                                            placeholder="Enter link"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                       <div class="mb-3" id="venueField">
                            <label class="form-label" for="venue">Venue</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">
                                    <i class="bx bx-buildings"></i>
                                </span>
                                <input
                                    type="text"
                                    id="venue"
                                    class="form-control @error('venue') is-invalid @enderror"
                                    name="venue"
                                    placeholder="Enter venue"
                                    value="{{ old('venue') }}"
                                />
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
    <button type="submit" class="mt-3 btn btn-primary d-flex gap-2" id="createMeetingBtn">
        <i class='bx bx-plus' ></i>
        <span>Create Meeting</span>
    </button>
</form>
<script src="{{asset('assets/js/meetings.js')}}"></script>

@endsection 
