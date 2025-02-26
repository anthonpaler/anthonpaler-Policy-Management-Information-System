@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <div class="card">
          <div class="card-body">
              <div class="logo-con mb-5">
                  <div class="">
                      <div class="logo">
                          <img src="{{asset('assets/img/icons/brands/slsu_logo.png')}}" alt="">
                      </div>
                      <div>
                          <h3 class="m-0">PolMIS.</h3>
                      </div>
                  </div>
                  <hr>
                  <small class="text-muted">Policy Management Information System</small>
              </div>
              <h4 class="login-action-label">Sign in</h4>
              <span class="">Please sign-in using your HRMIS email</span>
              <form id="formAuthentication" class="mb-3 mt-4" action="" method="POST">
                  <div class="mb-3">
                      {{-- <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button> --}}
                      <div id="g_id_onload" 
                      data-client_id="{{ env('GOOGLE_CLIENT_ID') }}" 
                      data-callback="onSignIn"></div>
                      <div class="g_id_signin form-control" data-type="standard"></div>
                  </div>
              </form>
          </div>
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>
@endsection
