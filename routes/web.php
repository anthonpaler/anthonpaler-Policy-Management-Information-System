<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\OrderOfBusinessController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\Admin\AdminDashboard;




// Main Page Route
// Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');

Route::get('/', function () {
    return view('content.auth.auth-login');
  });

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/google-login', [LoginController::class, 'handleGoogleLogin'])->name('auth.google.login');
Route::post('/logout', [LoginController::class, 'destroy'])->name('auth.logout');



Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->group(function() {
  Route::get('/admin-dashboard', [AdminDashboard::class, 'index'])->name('super_admin.dashboard');
});

// PROPONENT
Route::middleware(['auth', 'proponents'])->prefix('proponents')->group(function() {
  // DASHBOARD ROUTES
  Route::get('/dashboard', [Analytics::class, 'index'])->name('proponent.dashboard');

  // MEETINGS ROUTES
  Route::get('/meetings', [MeetingController::class, 'viewMeetings'])->name('proponent.meetings');
  Route::get('/meetings/meeting-details/{level}/{meeting_id}', [MeetingController::class, 'viewMeetingDetails']
    )->name('proponent.meetings.details');
  Route::get('/meetings/submit-proposal/{level}/{meeting_id}', [ProposalController::class, 'viewSubmitProposal'])->name('proponent.meetings.submit-proposal');
  Route::post('/meetings/filter', [MeetingController::class, 'filterMeetings'])->name(name: 'proponent.meetings.filter');

  // PROPOSALS ROUTES
  Route::post('/proposals/store/{meeting_id}', [ProposalController::class, 'submitProposal'])->name('proponent.proposals.store');
  Route::post('/projects/media', [ProposalController::class, 'storeMedia'])->name('proponent.projects.storeMedia');
  Route::post('/projects/media/delete', [ProposalController::class, 'deleteMedia'])->name('proponent.media.delete');
  Route::get('/search-users', [ProposalController::class, 'searchUsers'])->name('proponent.search-users');
  Route::get('/my-proposals', [ProposalController::class, 'viewMyProposals'])->name('proponent.proposals');
  Route::get('/my-proposal/edit-proposal/{proposal_id}', [ProposalController::class, 'viewEditProposal'])->name('proponent.proposal.edit');
  Route::get('/my-proposals', [ProposalController::class, 'viewMyProposals'])->name('proponent.proposals');
  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails'])->name('proponent.proposal.details');
  Route::post('/proposals/edit/{proposal_id}', [ProposalController::class, 'editProposal'])->name('proponent.proposal.edit.save');
  Route::post('/proposal/delete', [ProposalController::class, 'deleteProposal'])->name('proponent.proposal.delete');

  // ORDER OF BUSINESS ROUTES
});

// LOCAL SECRETARY
Route::middleware(['auth', 'local_secretary'])->prefix('local-campus-secretary')->group(function() {
  // DASHBOARD ROUTES
  Route::get('/dashboard', [Analytics::class, 'index'])->name('local_sec.dashboard');
  
  // MEETINGS ROUTES
  Route::get('/meetings', [MeetingController::class, 'viewMeetings'])->name('local_sec.meetings');
  Route::get('/meetings/create-meeting', [MeetingController::class, 'viewCreateMeeting'])->name('local_sec.view_create_meeting');
  Route::post('/meetings/create', [MeetingController::class, 'createMeeting'])->name('local_sec.meetings.create');
  Route::get('/meetings/meeting-details/{level}/{meeting_id}', [MeetingController::class, 'viewMeetingDetails']
  )->name('local_sec.meetings.details');
  Route::get('/meetings/edit/{level}/{meeting_id}', [MeetingController::class, 'viewEditMeeting'])->name('local_sec.meeting.edit_meeting');
  Route::post('/meetings/save-edit/{level}/{meeting_id}', [MeetingController::class, 'EditMeeting'])->name('local_sec.meetings.save-edit');
  Route::post('/meetings/filter', [MeetingController::class, 'filterMeetings'])->name(name: 'local_sec.meetings.filter');
  Route::get('/meetings/view-submit-proposal/{level}/{meeting_id}',[ProposalController::class, 'viewSubmitProposalSecretary'])->name('local_sec.submit.proposal.secretary');
  Route::post('/meetings/submit/proposal/{level}/{meeting_id}',[ProposalController::class, 'submitProposalSecretary'])->name('local_sec.proposal.submit');
  Route::get('/meetings/view-generate-oob/{level}/{meeting_id}', [OrderOfBusinessController::class, 'viewGenerateOOB'])->name('local_sec.order_of_business.view-generate');
  Route::post('/meetings/generate-oob/{level}/{meeting_id}', [OrderOfBusinessController::class, 'generateOOB'])->name('local_sec.order_of_business.generate');


  // PROPOSALS ROUTES
  Route::post('/proposals/edit/{proposal_id}', [ProposalController::class, 'editProposal'])->name('local_sec.proposal.edit.save');  // FINAL EDIT PROPOSAL
  Route::get('/proposals', [ProposalController::class, 'viewMeetingsWithProposalCount'])->name('local_sec.proposals');
  Route::get('/meetings/proposals/{level}/{meeting_id}', [ProposalController::class, 'viewMeetingProposals'])->name('local_sec.meetings.proposals');
  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails_Secretary'])->name('local_sec.proposal.details');
  Route::post('/proposal/edit/{proposal_id}', [ProposalController::class, 'editProposalSecretary'])->name('local_sec.proposal.edit');

  // ORDER OF BUSINESS ROUTES
  Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('local_sec.order-of-business');
  Route::post('/oob/filter', [OrderOfBusinessController::class, 'filterOOB'])->name(name: 'local_sec.oob.filter');
  Route::get('/meetings/view-order-of-business/{level}/{oob_id}', [OrderOfBusinessController::class, 'viewOOB'])->name('local_sec.order_of_business.view-oob');
  Route::post('/order-of-business/save/{oob_id}', [OrderOfBusinessController::class, 'saveOOB'])->name('local_sec.order_of_business.save');
  Route::post('/order-of-business/disseminate/{level}/{oob_id}', [OrderOfBusinessController::class, 'disseminateOOB'])->name('local_sec.dissemenate.order_of_business');
});


// UNIVERSITY SECRETARY
Route::middleware(['auth', 'university_secretary'])->prefix('university-secretary')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('univ_sec.dashboard');
  Route::get('/meetings', [MeetingController::class, 'viewMeetings'])->name('univ_sec.meetings');
  Route::get('/meetings/create-meeting', [MeetingController::class, 'viewCreateMeeting'])->name('univ_sec.view_create_meeting');
  Route::post('/meetings/create', [MeetingController::class, 'createMeeting'])->name('univ_sec.meetings.create');
  Route::get('/meetings/meeting-details/{level}/{meeting_id}', [MeetingController::class, 'viewMeetingDetails']
  )->name('univ_sec.meetings.details');

  Route::get('/meetings/edit/{level}/{meeting_id}', [MeetingController::class, 'viewEditMeeting'])->name('univ_sec.meeting.edit_meeting');

  Route::post('/meetings/save-edit/{level}/{meeting_id}', [MeetingController::class, 'EditMeeting'])->name('univ_sec.meetings.save-edit');
  Route::get('/proposals', [ProposalController::class, 'viewMeetingsWithProposalCount'])->name('univ_sec.proposals');

  Route::get('/meetings/view-generate-oob/{level}/{meeting_id}', [OrderOfBusinessController::class, 'viewGenerateOOB'])->name('univ_sec.order_of_business.view-generate');
  Route::post('/meetings/generate-oob/{level}/{meeting_id}', [OrderOfBusinessController::class, 'generateOOB'])->name('univ_sec.order_of_business.generate');

  Route::get('/meetings/proposals/{level}/{meeting_id}', [ProposalController::class, 'viewMeetingProposals'])->name('univ_sec.meetings.proposals');
  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails_Secretary'])->name('univ_sec.proposal.details');
  Route::post('/proposal/edit/{proposal_id}', [ProposalController::class, 'editProposalSecretary'])->name('univ_sec.proposal.edit');

  Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('univ_sec.order-of-business');
  Route::post('/oob/filter', [OrderOfBusinessController::class, 'filterOOB'])->name(name: 'univ_sec.oob.filter');
  Route::get('/meetings/view-order-of-business/{level}/{oob_id}', [OrderOfBusinessController::class, 'viewOOB'])->name('univ_sec.order_of_business.view-oob');

  Route::post('/order-of-business/save/{oob_id}', [OrderOfBusinessController::class, 'saveOOB'])->name('univ_sec.order_of_business.save');
  Route::post('/order-of-business/disseminate/{level}/{oob_id}', [OrderOfBusinessController::class, 'disseminateOOB'])->name('univ_sec.dissemenate.order_of_business');

  
  Route::post('/meetings/filter', [MeetingController::class, 'filterMeetings'])->name(name: 'univ_sec.meetings.filter');

  Route::get('/meetings/view-submit-proposal/{level}/{meeting_id}',[ProposalController::class, 'viewSubmitProposalSecretary'])->name('univ_sec.submit.proposal.secretary');

  Route::post('/meetings/submit/proposal/{level}/{meeting_id}',[ProposalController::class, 'submitProposalSecretary'])->name('univ_sec.proposal.submit');

  Route::post('/proposals/edit/{proposal_id}', [ProposalController::class, 'editProposal'])->name('univ_sec.proposal.edit.save');  // FINAL EDIT PROPOSAL
});

// BOARD SECRETARY ROUTES 
Route::middleware(['auth', 'board_secretary'])->prefix('board-secretary')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('board_sec.dashboard');
  Route::get('/meetings', [MeetingController::class, 'viewMeetings'])->name('board_sec.meetings');
  Route::get('/meetings/create-meeting', [MeetingController::class, 'viewCreateMeeting'])->name('board_sec.view_create_meeting');
  Route::post('/meetings/create', [MeetingController::class, 'createMeeting'])->name('board_sec.meetings.create');
  Route::get('/meetings/meeting-details/{level}/{meeting_id}', [MeetingController::class, 'viewMeetingDetails']
  )->name('board_sec.meetings.details');

  Route::get('/meetings/edit/{level}/{meeting_id}', [MeetingController::class, 'viewEditMeeting'])->name('board_sec.meeting.edit_meeting');

  Route::post('/meetings/save-edit/{level}/{meeting_id}', [MeetingController::class, 'EditMeeting'])->name('board_sec.meetings.save-edit');
  Route::get('/proposals', [ProposalController::class, 'viewMeetingsWithProposalCount'])->name('board_sec.proposals');

  Route::get('/meetings/view-generate-oob/{level}/{meeting_id}', [OrderOfBusinessController::class, 'viewGenerateOOB'])->name('board_sec.order_of_business.view-generate');
  Route::post('/meetings/generate-oob/{level}/{meeting_id}', [OrderOfBusinessController::class, 'generateOOB'])->name('board_sec.order_of_business.generate');

  Route::get('/meetings/proposals/{level}/{meeting_id}', [ProposalController::class, 'viewMeetingProposals'])->name('board_sec.meetings.proposals');
  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails_Secretary'])->name('board_sec.proposal.details');
  Route::post('/proposal/edit/{proposal_id}', [ProposalController::class, 'editProposalSecretary'])->name('board_sec.proposal.edit');

  Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('board_sec.order-of-business');
  Route::post('/oob/filter', [OrderOfBusinessController::class, 'filterOOB'])->name(name: 'board_sec.oob.filter');
  Route::get('/meetings/view-order-of-business/{level}/{oob_id}', [OrderOfBusinessController::class, 'viewOOB'])->name('board_sec.order_of_business.view-oob');

  Route::post('/order-of-business/save/{oob_id}', [OrderOfBusinessController::class, 'saveOOB'])->name('board_sec.order_of_business.save');
  Route::post('/order-of-business/disseminate/{level}/{oob_id}', [OrderOfBusinessController::class, 'disseminateOOB'])->name('board_sec.dissemenate.order_of_business');

  Route::post('/meetings/filter', [MeetingController::class, 'filterMeetings'])->name(name: 'board_sec.meetings.filter');

  Route::post('/proposals/edit/{proposal_id}', [ProposalController::class, 'editProposal'])->name('board_sec.proposal.edit.save');  // FINAL EDIT PROPOSAL
});


Route::get('/sample', function () {
  return view('content.sample');
});

// TO BE ARANGED ROUTES
Route::post('/proposals/update-selected-proposal-status', [ProposalController::class, 'updateSelectedProposalStatus'])->name('proposals.update_selected_proposal_status');

Route::post('/proposals/update-proposal-status', [ProposalController::class, 'updateProposalStatus'])->name('proposals.update_proposal_status');

Route::get('/order-of-business/pdf/{level}/{oob_id}', [OrderOfBusinessController::class, 'exportOOB_PDF'])->name('oob.export.pdf');

Route::post('/delete-proposal-file', [ProposalController::class, 'deleteFile']);

Route::post('/rename-proposal-file', [ProposalController::class, 'renameFile'])->name('rename.proposal.file');

Route::post('/update-proposal-file-order', [ProposalController::class, 'updateOrder']);

Route::post('/update-proposal-order/{level}', [OrderOfBusinessController::class, 'updateProposalOrder'])->name('update_proposal_order');

Route::post('/save-proposal-group/{level}', [OrderOfBusinessController::class, 'saveProposalGroup'])->name('save_proposal_group');

Route::post('/ungroup-proposal/{level}', [OrderOfBusinessController::class, 'ungroupProposal'])->name('ungroup_proposal');

Route::post('/update-proposal-group/{level}', [OrderOfBusinessController::class, 'updateProposalGroup'])->name('update_proposal_group');




