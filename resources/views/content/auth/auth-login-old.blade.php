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
                    <!-- <div class="app-brand justify-content-center">
                        <a href="/" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('assets/img/system/124.png') }}" alt="slsu logo" height="300" width="300" class="img-fluid">
                        </span>
                        </a>
                    </div> -->
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
        <script>
            function decodeJwtResponse(token) {
                let base64Url = token.split('.')[1];
                let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                let jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));
                return JSON.parse(jsonPayload);
            }

            window.onSignIn = googleUser => {
                var user = decodeJwtResponse(googleUser.credential);
                console.log(user);
                if (user) {
                    $.ajax({
                        url: "{{ route('auth.google.login') }}",
                        method: 'POST',
                        data: {
                            email: user.email,
                            name: user.name,
                            image: user.picture,
                            google_id: user.sub,
                            _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                        },
                        beforeSend: function() {
                            toastr.info('Logging in with Google, please wait...');
                        },
                        success: function(response) {
                        toastr.success(response.message);
                        if (response.success) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        }
                    },
                        error: function(xhr, status, error) {
                        console.log(xhr.responseText); // This will provide more detail about the 500 error.
                        toastr.error('Employee Record not found.');
                        }
                    });
                } else {
                    toastr.error('Google login failed. Please try again.');
                }
            };
        </script>
@endsection
