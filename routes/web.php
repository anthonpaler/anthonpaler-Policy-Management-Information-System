<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\tables\Basic as TablesBasic;

use App\Http\Controllers\MeetingController;
use App\Http\Controllers\OrderOfBusinessController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\dashboard\Analytics;



// Main Page Route
// Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');

Route::get('/', function () {
    return view('content.auth.auth-login');
  });

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/google-login', [LoginController::class, 'handleGoogleLogin'])->name('auth.google.login');
Route::post('/logout', [LoginController::class, 'destroy'])->name('auth.logout');



Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('super_admin.dashboard');
});

// PROPONENT
Route::middleware(['auth', 'proponents'])->prefix('proponents')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('proponent.dashboard');
  Route::get('/meetings', [MeetingController::class, 'viewMeetings'])->name('proponent.meetings');
  Route::get('/meetings/meeting-details/{level}/{meeting_id}', [MeetingController::class, 'viewMeetingDetails']
    )->name('proponent.meetings.details');
  Route::get('/meetings/submit-proposal/{level}/{meeting_id}', [ProposalController::class, 'viewSubmitProposal'])->name('proponent.meetings.submit-proposal');
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
});

// LOCAL SECRETARY
Route::middleware(['auth', 'local_secretary'])->prefix('local-campus-secretary')->group(function() {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('local_sec.dashboard');
  Route::get('/meetings', [MeetingController::class, 'viewMeetings'])->name('local_sec.meetings');
  Route::get('/meetings/create-meeting', [MeetingController::class, 'viewCreateMeeting'])->name('local_sec.view_create_meeting');
  Route::post('/meetings/create', [MeetingController::class, 'createMeeting'])->name('local_sec.meetings.create');
  Route::get('/meetings/meeting-details/{level}/{meeting_id}', [MeetingController::class, 'viewMeetingDetails']
  )->name('local_sec.meetings.details');

  Route::get('/meetings/edit/{level}/{meeting_id}', [MeetingController::class, 'viewEditMeeting'])->name('local_sec.meeting.edit_meeting');

  Route::post('/meetings/save-edit/{level}/{meeting_id}', [MeetingController::class, 'EditMeeting'])->name('local_sec.meetings.save-edit');
  Route::get('/proposals', [ProposalController::class, 'viewMeetingsWithProposalCount'])->name('local_sec.proposals');

  Route::get('/meetings/view-generate-oob/{level}/{meeting_id}', [OrderOfBusinessController::class, 'viewGenerateOOB'])->name('local_sec.order_of_business.view-generate');
  Route::post('/meetings/generate-oob/{level}/{meeting_id}', [OrderOfBusinessController::class, 'generateOOB'])->name('local_sec.order_of_business.generate');

  Route::get('/meetings/proposals/{level}/{meeting_id}', [ProposalController::class, 'viewMeetingProposals'])->name('local_sec.meetings.proposals');
  Route::get('/proposals/details/{proposal_id}', [ProposalController::class, 'viewProposalDetails_Secretary'])->name('local_sec.proposal.details');
  Route::post('/proposal/edit/{proposal_id}', [ProposalController::class, 'editProposalSecretary'])->name('local_sec.proposal.edit');

  Route::get('/order-of-business', [OrderOfBusinessController::class, 'viewOOBList' ])->name('local_sec.order-of-business');
  Route::post('/oob/filter', [OrderOfBusinessController::class, 'filterOOB'])->name(name: 'local_sec.oob.filter');
  Route::get('/meetings/view-order-of-business/{level}/{oob_id}', [OrderOfBusinessController::class, 'viewOOB'])->name('local_sec.order_of_business.view-oob');

  Route::post('/order-of-business/save/{oob_id}', [OrderOfBusinessController::class, 'saveOOB'])->name('local_sec.order_of_business.save');
  Route::post('/order-of-business/disseminate/{level}/{oob_id}', [OrderOfBusinessController::class, 'disseminateOOB'])->name('local_sec.dissemenate.order_of_business');
});


Route::get('/sample', function () {
  return view('content.sample');
});

// TO BE ARANGED ROUTES
Route::post('/proposals/update-selected-proposal-status', [ProposalController::class, 'updateSelectedProposalStatus'])->name('proposals.update_selected_proposal_status');

Route::post('/proposals/update-proposal-status', [ProposalController::class, 'updateProposalStatus'])->name('proposals.update_proposal_status');

Route::get('/order-of-business/pdf/{oob_id}', [OrderOfBusinessController::class, 'generatePDF'])->name('order_of_business.pdf');

Route::post('/delete-proposal-file', [ProposalController::class, 'deleteFile']);

Route::post('/rename-proposal-file', [ProposalController::class, 'renameFile'])->name('rename.proposal.file');



















































// layout
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// pages
Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

// cards
Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');

// User Interface
Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

// extended ui
Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');

// icons
Route::get('/icons/boxicons', [Boxicons::class, 'index'])->name('icons-boxicons');

// form elements
Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');

// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');

// tables
Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');
