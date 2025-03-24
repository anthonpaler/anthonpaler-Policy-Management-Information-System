<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\MeetingsController;
use App\Http\Controllers\OrderOfBusinessController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserManagementController;


Route::get('/', function () {
  return view('auth.auth-login');
});

// Route::get('/linkstorage', function () {
//   Artisan::call('storage:link');
// });

Route::get('/clear-route-cache', function () {
  Artisan::call('route:clear');
  return 'Route cache cleared';
});


Route::get('/clear-cache', function () {
   Artisan::call('cache:clear');
   return "Cache cleared successfully";
});


// AUTH ROUTES
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/google-login', [LoginController::class, 'handleGoogleLogin'])->name('auth.google.login');
Route::post('/logout', [LoginController::class, 'destroy'])->name('auth.logout');


// Super ADMIN
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('super_admin.dashboard');

Route::get('/manage', [UserManagementController::class, 'index'])->name('super_admin.manage');
Route::get('/meetings', [MeetingsController::class, 'viewMeetings'])->name('super_admin.meetings');
Route::get('/proposals', [ProposalController::class, 'viewProposals'])->name('super_admin.proposals');
Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('super_admin.order-of-business');

Route::get('/get-emails', [UserManagementController::class, 'getEmails'])->name('getEmails');
Route::post('/store-email', [UserManagementController::class, 'storeEmail'])->name('storeEmail');

Route::get('/get-users', [UserManagementController::class, 'getUsers'])->name('getUsers');

Route::post('/user/store', [UserManagementController::class, 'store'])->name('user.store');

});



// LOCAL SECRETARY ROUTES 
Route::middleware(['auth', 'local_campus_secretary'])->prefix('local-campus-secretary')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('local_sec.dashboard');
  Route::get('/meetings', [MeetingsController::class, 'viewMeetings'])->name('local_sec.meetings');
  Route::get('/meetings/create-meeting', [MeetingsController::class, 'viewCreateMeeting'])->name('local_sec.create_meeting');
  Route::post('/meetings/set', [MeetingsController::class, 'createMeeting'])->name('local_sec.meetings.set');
  Route::post('/meetings/filter', [MeetingsController::class, 'filterMeetings'])->name( 'local_sec.meetings.filter');
  Route::get('/meetings/view-submit-proposal/{meeting_id}',[ProposalController::class, 'viewSubmitProposalSecretary'])->name('local_sec.submit.proposal.secretary');
  Route::post('/meetings/submit/proposal/{meeting_id}',[ProposalController::class, 'submitProposalSecretary'])->name('local_sec.proposal.submit');
  Route::get('/meetings/generate-order-of-business/{meeting_id}', [OrderOfBusinessController::class, 'viewGenerateOOB'])->name('local_sec.order_of_business.view-generate');
  Route::get('/meetings/view-order-of-business/{oob_id}', [OrderOfBusinessController::class, 'viewOOB'])->name('local_sec.order_of_business.view-oob');
  Route::post('/meetings/save-oob/{oob_id}', [OrderOfBusinessController::class, 'saveOOB'])->name('local_sec.order_of_business.save');
  Route::post('/meetings/generate-oob/{meeting_id}', [OrderOfBusinessController::class, 'generateOOB'])->name('local_sec.order_of_business.generate');
  Route::post('/meetings/disseminate-order-of-business/{oob_id}', [OrderOfBusinessController::class, 'disseminateOOB'])->name('local_sec.dissemenate.order_of_business');
  Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('local_sec.order-of-business');
  Route::get('/proposals', [ProposalController::class, 'viewProposals'])->name('local_sec.proposals');
  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails_Secretary'])->name('local_sec.proposal.details');
  Route::get('/meetings/proposals/{meeting_id}', [ProposalController::class, 'viewMeetingProposals'])->name('local_sec.meetings.proposals');
  Route::get('/meetings/edit/{meeting_id}', [MeetingsController::class, 'viewEditMeeting'])->name('local_sec.meetings.edit');
  Route::post('/meetings/save-edit/{meeting_id}', [MeetingsController::class, 'updateMeeting'])->name('local_sec.meetings.save-edit');

  // NEWLY ADDED
  Route::post('/proposals/filter', [ProposalController::class, 'filterMeetingProposls'])->name('local_sec.proposals.filter');

  Route::post('/proposal/edit/{proposal_id}', [ProposalController::class, 'editProposalSecretary'])->name('local_sec.proposal.edit');

  // NEW OOB FILTER 
  Route::post('/oob/filter', [OrderOfBusinessController::class, 'filterOOB'])->name( 'local_sec.oob.filter');

  // MARCH 07 2025
  Route::post('/proposals/store/{meeting_id}', [ProposalController::class, 'addProposal'])->name('local_sec.addProposal');
  Route::get('/fetch-proponents', [ProposalController::class, 'fetchProponents'])->name('local_sec.fetchProponents');
  Route::post('/projects/media', [ProposalController::class, 'storeMedia'])->name('local_sec.projects.storeMedia');
  Route::post('/projects/media/delete', [ProposalController::class, 'deleteMedia'])->name('local_sec.media.delete');

  Route::post('/proposals/update-order', [ProposalController::class, 'updateOrder'])->name('local_sec.proposals.updateOrder');
  Route::post('/order-of-business/upload-minutes', [OrderOfBusinessController::class, 'uploadPreviousMinutes'])->name('local_sec.upload.minutes');


});

// UNIVERSITY SECRETARY ROUTES 
Route::middleware(['auth', 'university_secretary'])->prefix('university-secretary')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('univ_sec.dashboard');
  Route::get('/meetings', [MeetingsController::class, 'viewMeetings'])->name('univ_sec.meetings');
  Route::get('/meetings/create-meeting', [MeetingsController::class, 'viewCreateMeeting'])->name('univ_sec.create_meeting');
  Route::post('/meetings/set', [MeetingsController::class, 'createMeeting'])->name('univ_sec.meetings.set');
  Route::post('/meetings/filter', [MeetingsController::class, 'filterMeetings'])->name('univ_sec.meetings.filter');
  Route::get('/meetings/view-submit-proposal/{meeting_id}',[ProposalController::class, 'viewSubmitProposalSecretary'])->name('univ_sec.submit.proposal.secretary');
  Route::post('/meetings/submit/proposal/{meeting_id}',[ProposalController::class, 'submitProposalSecretary'])->name('univ_sec.proposal.submit');
  Route::get('/meetings/generate-order-of-business/{meeting_id}', [OrderOfBusinessController::class, 'viewGenerateOOB'])->name('univ_sec.order_of_business.view-generate');
  Route::get('/meetings/view-order-of-business/{oob_id}', [OrderOfBusinessController::class, 'viewOOB'])->name('univ_sec.order_of_business.view-oob');
  Route::post('/meetings/save-oob/{oob_id}', [OrderOfBusinessController::class, 'saveOOB'])->name('univ_sec.order_of_business.save');
  Route::post('/meetings/generate-oob/{meeting_id}', [OrderOfBusinessController::class, 'generateOOB'])->name('univ_sec.order_of_business.generate');
  Route::post('/meetings/disseminate-order-of-business/{oob_id}', [OrderOfBusinessController::class, 'disseminateOOB'])->name('univ_sec.dissemenate.order_of_business');
  Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('univ_sec.order-of-business');
  Route::get('/proposals', [ProposalController::class, 'viewProposals'])->name('univ_sec.proposals');
  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails_Secretary'])->name('univ_sec.proposal.details');
  Route::get('/meetings/proposals/{meeting_id}', [ProposalController::class, 'viewMeetingProposals'])->name('univ_sec.meetings.proposals');
  Route::get('/meetings/edit/{meeting_id}', [MeetingsController::class, 'viewEditMeeting'])->name('univ_sec.meetings.edit');
  Route::post('/meetings/save-edit/{meeting_id}', [MeetingsController::class, 'updateMeeting'])->name('univ_sec.meetings.save-edit');

  // NEWLY ADDED
  Route::post('/proposals/filter', [ProposalController::class, 'filterMeetingProposls'])->name('univ_sec.proposals.filter');

  // NEW OOB FILTER 
  Route::post('/oob/filter', [OrderOfBusinessController::class, 'filterOOB'])->name( 'univ_sec.oob.filter');

  // MARCH 06 2025
  Route::post('/proposal/edit/{proposal_id}', [ProposalController::class, 'editProposalSecretary'])->name('univ_sec.proposal.edit');

  // MARCH 07 2025
  Route::post('/proposals/store-univ/{meeting_id}', [ProposalController::class, 'addProposal'])->name('univ_sec.addProposal');
  Route::get('/fetch-proponents', [ProposalController::class, 'fetchProponents'])->name('univ_sec.fetchProponents');


  Route::post('/projects/media', [ProposalController::class, 'storeMedia'])->name('univ_sec.projects.storeMedia');
  Route::post('/projects/media/delete', [ProposalController::class, 'deleteMedia'])->name('univ_sec.media.delete');


  Route::post('/proposals/update-order', [ProposalController::class, 'updateOrder'])->name('univ_sec.proposals.updateOrder');

  Route::post('/order-of-business/upload-minutes', [OrderOfBusinessController::class, 'uploadPreviousMinutes'])->name('univ_sec.upload.minutes');


});


// BOARD SECRETARY ROUTES 
Route::middleware(['auth', 'board_secretary'])->prefix('board-secretary')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('board_sec.dashboard');
  Route::get('/meetings', [MeetingsController::class, 'viewMeetings'])->name('board_sec.meetings');
  Route::get('/meetings/create-meeting', [MeetingsController::class, 'viewCreateMeeting'])->name('board_sec.create_meeting');
  Route::post('/meetings/set', action: [MeetingsController::class, 'createMeeting'])->name('board_sec.meetings.set');
  Route::post('/meetings/filter', [MeetingsController::class, 'filterMeetings'])->name('board_sec.meetings.filter');
  Route::get('/meetings/generate-order-of-business/{meeting_id}', [OrderOfBusinessController::class, 'viewGenerateOOB'])->name('board_sec.order_of_business.view-generate');
  Route::get('/meetings/view-order-of-business/{oob_id}', [OrderOfBusinessController::class, 'viewOOB'])->name('board_sec.order_of_business.view-oob');
  Route::post('/meetings/save-oob/{oob_id}', [OrderOfBusinessController::class, 'saveOOB'])->name('board_sec.order_of_business.save');
  Route::post('/meetings/generate-oob/{meeting_id}', [OrderOfBusinessController::class, 'generateOOB'])->name('board_sec.order_of_business.generate');
  Route::post('/meetings/disseminate-order-of-business/{oob_id}', [OrderOfBusinessController::class, 'disseminateOOB'])->name('board_sec.dissemenate.order_of_business');
  Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('board_sec.order-of-business');
  Route::get('/meetings/proposals/{meeting_id}', [ProposalController::class, 'viewMeetingProposals'])->name('board_sec.meetings.proposals');

  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails_Secretary'])->name('board_sec.proposal.details');

  Route::get('/proposals', [ProposalController::class, 'viewProposals'])->name('board_sec.proposals');
  Route::get('/meetings/edit/{meeting_id}', [MeetingsController::class, 'viewEditMeeting'])->name('board_sec.meetings.edit');
  Route::post('/meetings/save-edit/{meeting_id}', [MeetingsController::class, 'updateMeeting'])->name('board_sec.meetings.save-edit');

  // NEWLY ADDED
  Route::post('/proposals/filter', [ProposalController::class, 'filterMeetingProposls'])->name('board_sec.proposals.filter');

  // NEW OOB FILTER 
  Route::post('/oob/filter', [OrderOfBusinessController::class, 'filterOOB'])->name( 'board_sec.oob.filter');

  // MARCH 06 2025
  Route::post('/proposal/edit/{proposal_id}', [ProposalController::class, 'editProposalSecretary'])->name('board_sec.proposal.edit');

// MARCH 07 2025
  Route::post('/proposals/store-univ/{meeting_id}', [ProposalController::class, 'addProposal'])->name('board_sec.addProposal');
  Route::get('/fetch-proponents', [ProposalController::class, 'fetchProponents'])->name('board_sec.fetchProponents');


  Route::post('/projects/media', [ProposalController::class, 'storeMedia'])->name('board_sec.projects.storeMedia');
  Route::post('/projects/media/delete', [ProposalController::class, 'deleteMedia'])->name('board_sec.media.delete');


  Route::post('/proposals/update-order', [ProposalController::class, 'updateOrder'])->name('board_sec.proposals.updateOrder');

  Route::post('/order-of-business/upload-minutes', [OrderOfBusinessController::class, 'uploadPreviousMinutes'])->name('board_sec.upload.minutes');



});


// PROPONENTS ROUTES
Route::middleware(['auth', 'proponents'])->prefix('proponents')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('proponent.dashboard');
  Route::get('/meetings', [MeetingsController::class, 'viewMeetings'])->name('proponent.meetings');
  Route::get('/meetings/submit-proposal/{meeting_id}', [ProposalController::class, 'viewSubmitProposal'])->name('proponent.meetings.submit-proposal');
  Route::post('/meetings/filter', [MeetingsController::class, 'filterMeetings'])->name( 'proponent.meetings.filter');
  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails'])->name('proponent.proposal.details');
  Route::get('/my-proposals/proposal-details/{id}', [ProposalController::class, 'viewProposalDetails'])->name('proponent.proposals');
  Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('proponent.order-of-business');
  Route::get('/meetings/view-order-of-business/{oob_id}', [OrderOfBusinessController::class, 'viewOOB'])->name('proponent.order_of_business.view-oob');
  Route::post('/meetings/save-oob/{oob_id}', [OrderOfBusinessController::class, 'saveOOB'])->name('proponent.order_of_business.save');
  Route::post('/meetings/generate-oob/{meeting_id}', [OrderOfBusinessController::class, 'generateOOB'])->name('proponent.order_of_business.generate');
  Route::post('/meetings/disseminate-order-of-business/{oob_id}', [OrderOfBusinessController::class, 'disseminateOOB'])->name('proponent.dissemenate.order_of_business');
  Route::get('/meetings/proposals/{meeting_id}', [ProposalController::class, 'viewMeetingProposals'])->name('proponent.meetings.proposals');
  Route::post('/proposals/store/{meeting_id}', [ProposalController::class, 'submitProposal'])->name('proponent.proposals.store');


  Route::post('/proposals/edit/{proposal_id}', [ProposalController::class, 'editProposal'])->name('proponent.proposal.edit.save');
  


  Route::post('/projects/media', [ProposalController::class, 'storeMedia'])->name('proponent.projects.storeMedia');
  Route::post('/projects/media/delete', [ProposalController::class, 'deleteMedia'])->name('proponent.media.delete');
  Route::get('/search-users', [ProposalController::class, 'searchUsers'])->name('proponent.search-users');
  Route::get('/my-proposals', [ProposalController::class, 'viewMyProposals'])->name('proponent.proposals');
  Route::get('/my-proposal/edit-proposal/{proposal_id}', [ProposalController::class, 'viewEditProposal'])->name('proponent.proposal.edit');

  // NEW OOB FILTER 
  Route::post('/oob/filter', [OrderOfBusinessController::class, 'filterOOB'])->name('proponent.oob.filter');

  Route::post('/order-of-business/upload-minutes', [OrderOfBusinessController::class, 'uploadPreviousMinutes'])->name('proponent.upload.minutes');


  Route::post('/proposals/update-order', [ProposalController::class, 'updateOrder'])->name('proponent.proposals.updateOrder');

});



// To Be Arange Routes
Route::post('/proposals/update-selected-proposal-status', [ProposalController::class, 'updateSelectedProposalStatus'])->name('proposals.update_selected_proposal_status');

Route::post('/proposals/update-proposal-status', [ProposalController::class, 'updateProposalStatus'])->name('proposals.update_proposal_status');

Route::post('/proposal/update/return', [ProposalController::class, 'returnProposal'])->name('proposals.return');

Route::post('/proposal/update/deferred', [ProposalController::class, 'deferredProposal'])->name('proposals.deferred');

Route::get('/order-of-business/pdf/{oob_id}', [OrderOfBusinessController::class, 'generatePDF'])->name('order_of_business.pdf');

Route::get('/get-previous-minutes/{meeting_id}', [OrderOfBusinessController::class, 'getPreviousMinutes'])->name('get.previous.minutes');


// FOR EDIT PROPOSAL
Route::post('/reupload-proposal-file', [ProposalController::class, 'reuploadFile']);

Route::post('/delete-proposal-file', [ProposalController::class, 'deleteFile']);





Route::get('/sample', function () {
  return view('content.sample');
})->name('proposals');

Route::get('/meetings/meeting-details/{meeting_id}', [MeetingsController::class, 'viewMeetingDetails']
)->name('meetings.details');

Route::get('/error/401', function () {
  return view('components.401');
});


Route::get('/proposal-details', function () {
  return view('content.proposals.viewProposal');
})->name('proposals');




