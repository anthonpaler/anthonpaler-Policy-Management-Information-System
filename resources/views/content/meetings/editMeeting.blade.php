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
    <a href="{{ route(    getUserRole().'.meetings') }}">Meetings</a>
    <i class='bx bx-chevron-right' ></i>
    <a href="#">Edit Meetings</a>
</div>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Meeting</h5>
            <small class="text-muted float-end">Please input the details accordingly.</small>
        </div>
        <div class="card-body">
        <form method="POST" action="{{route( getUserRole().'.meetings.save-edit', ['meeting_id' => $meetings->id])}}" id="meetingForm">
            @csrf
                <!-- Quarter / Year / Date & Time -->
                <div class="mb-3">
                    <label class="form-label" for="basic-icon-default-email">Quarter / Year </label>
                    <div class="row g-3">
                        <!-- Quarter -->
                        <div class="col-md-6">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text">
                                    <i class="bx bx-buildings"></i>
                                </span>

                                <select class="form-control @error('quarter') is-invalid @enderror" name="quarter" required disabled>
                                  @foreach (config('meetings.quaterly_meetings') as $index => $item)
                                    <option value="{{ $index }}"
                                        {{ (isset($meetings) && (int) $meetings->quarter === (int) $index) ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach

                              </select>
                              @error('quarter')
                                  <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                              @enderror

                            </div>
                        </div>

                        <!-- Year -->
                        <div class="col-md-6">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text">
                                    <i class="bx bx-buildings"></i>
                                </span>
                                <select class="form-control @error('year') is-invalid @enderror" name="year" required disabled>
                                    @for ($year = date('Y'); $year <= date('Y') + 5; $year++)
                                        <option value="{{ $year }}"
                                            {{ (isset($meetings) && (int) $meetings->year === (int) $year) ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('year')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="basic-icon-default-company">Submission Start / End</label>
                    <div class="row g-3">
                    <div class="col-md-6">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-submission-start" class="input-group-text">
                                    <i class="bx bx-calendar"></i>
                                </span>
                                <input
                                    type="date"
                                    id="submission_start"
                                    class="form-control basic-icon-default-company @error('submission_start') is-invalid @enderror"
                                    name="submission_start"
                                    value="{{ old('submission_start', isset($meetings) && $meetings->submission_start ? (new DateTime($meetings->submission_start, new DateTimeZone('Asia/Manila')))->format('Y-m-d') : (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d')) }}"
                                    required
                                />
                                @error('submission_start')
                                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submission End -->
                        <div class="col-md-6">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-submission-end" class="input-group-text">
                                    <i class="bx bx-calendar"></i>
                                </span>
                                <input
                                    type="date"
                                    id="submission_end"
                                    class="form-control basic-icon-default-company @error('submission_end') is-invalid @enderror"
                                    name="submission_end"
                                    value="{{ old('submission_end', isset($meetings) && $meetings->submission_end ? (new DateTime($meetings->submission_end, new DateTimeZone('Asia/Manila')))->format('Y-m-d') : (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d')) }}"
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

                <!-- Meeting Type -->
                <div class="mb-3">
                    <div class="row g-3">
                      <div class="col-md-6">
                      <label class="form-label" for="basic-icon-default-phone">Council Types</label>
                        <div class="input-group input-group-merge">
                            <span id="basic-icon-default-phone2" class="input-group-text">
                                <i class='bx bx-user-pin'></i>
                            </span>
                            <select class="form-control @error('type') is-invalid @enderror" name="council_type" required>
                                <option value="">Select Meeting Type</option>

                                  @if (auth()->user()->role == 3)
                                      @foreach (config('meetings.council_types.local_level') as $index => $item)
                                      <option value="{{ $index }}"
                                        {{ (isset($meetings) && (int) $meetings->council_type === (int) $index) ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                      @endforeach
                                  @endif
                                  @if (auth()->user()->role == 4)
                                      @foreach (config('meetings.council_types.university_level') as $index => $item)
                                      <option value="{{ $index }}"
                                        {{ (isset($meetings) && (int) $meetings->council_type === (int) $index) ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                      @endforeach
                                  @endif
                                  @if (auth()->user()->role == 5)
                                      @foreach (config('meetings.council_types.board_level') as $index => $item)
                                      <option value="{{ $index }}"
                                        {{ (isset($meetings) && (int) $meetings->council_type === (int) $index) ? 'selected' : '' }}>
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

                      <div class="col-md-6">
                      <label class="form-label" for="basic-icon-default-phone">Status</label>
                        <div class="input-group input-group-merge">
                            <span id="basic-icon-default-phone2" class="input-group-text">
                                <i class='bx bx-user-pin'></i>
                            </span>
                            <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                                @foreach (config('meetings.status') as $index => $item)
                                    <option value="{{ $index }}"
                                        {{ (isset($meetings) && (int) $meetings->status === (int) $index) ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>
                      </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="basic-icon-default-message">Description & Other Reminders (Optional)</label>
                    <div class="input-group input-group-merge">
                        <span id="basic-icon-default-message2" class="input-group-text">
                            <i class="bx bx-comment"></i>
                        </span>
                        <textarea
                            id="basic-icon-default-message"
                            class="form-control"
                            placeholder="Please don't be late for we have a lot to discuss."
                            aria-label="Please don't be late for we have a lot to discuss."
                            aria-describedby="basic-icon-default-message2"
                            name="description"
                          >{{ old('description', isset($meetings) && $meetings->description ? $meetings->description : '') }}
                        </textarea>
                        @error('description')
                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="meeting-info-con" id="meetingInfo">
                    <div class="row g-2 mb-3">
                        <!-- Meeting Status -->
                        <!-- <div class="col-md-6"> -->
                            <label class="form-label" for="status">Modality</label>
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-company2" class="input-group-text">
                                    <i class="bx bx-buildings"></i>
                                </span>
                                <select
                                id="modality"
                                class="form-control @error('status') is-invalid @enderror"
                                name="modality"
                                >
                                <option value="">Select Modality</option>
                                @foreach (config('meetings.modalities') as $index => $item)
                                    <option value="{{ $index }}"
                                        {{ old('modality', isset($meetings) ? $meetings->modality : '') == $index ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                                </select>
                            @error('status')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                            </div>
                    </div>

                    <!-- Venue -->
                    <div class="mb-3 d-none" id="onlineModeInfo">
                        <label class="form-label" for="basic-icon-default-company">Platform if Online Modality</label>
                        <div class="row g-2">
                            <!-- First Input Group -->
                            <div class="col-md-6">
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
                                            {{ (isset($meetings) && (int) $meetings->mode_if_online === (int) $index) ? 'selected' : '' }}>
                                            {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('modality')
                                        <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Second Input Group -->
                            <div class="col-md-6">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-company2" class="input-group-text">
                                        <i class='bx bx-link' ></i>
                                    </span>
                                    <input
                                        id="mode_if_online"
                                        class="form-control basic-icon-default-company @error('venue') is-invalid @enderror"
                                        name="link"
                                        placeholder="Enter link"
                                        value="{{ old('description', isset($meetings) && $meetings->link ? $meetings->link : '') }}"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="venueField">
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
                                        {{ (isset($meetings) && $meetings->venue == $venue->id) ? 'selected' : (old('venue') == $venue->id ? 'selected' : "") }}>
                                        {{ $venue->description }}
                                    </option>
                                @endforeach
                            </select>
                            @error('venue')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                      <!-- Meeting Date & Timeear -->
                      <div class="">
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
                                  value="{{ isset($meetings) && $meetings->meeting_date_time ? (new DateTime($meetings->meeting_date_time, new DateTimeZone('Asia/Manila')))->format('Y-m-d\TH:i') : '' }}"
                                  min="{{ date('Y-m-d') }}"
                              />
                              @error('meeting_date_time')
                                  <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                              @enderror
                          </div>
                      </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="mt-3 btn btn-primary d-flex gap-2" id="editMeetingBtn">
                    <i class='bx bx-save'></i>
                    <span>Save Changes</span>
                </button>
            </form>
        </div>
    </div>
</div>
</div>
<script src="{{asset('assets/js/meetings.js')}}"></script>
@endsection
