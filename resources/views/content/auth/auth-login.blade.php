@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<style>
     .auth-con {
        background-image: url('{{ asset('assets/img/backgrounds/login_main_bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh; /* Ensure it covers the full viewport height */
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<div class="auth-con">
    <div class="auth-card">
        <div class="auth-form-con">
            <div class="auth-form-content">
                <div class="logo-wrapper">
                    <h2>PolMIS</h2>
                    <span>Policy Management Information System</span>
                </div>
                <div class="auth-header">
                    <div class="">
                        <h1>Hello,</h1>
                        <h1>Welcome to PolMIS!</h1>
                    </div>
                    <span>Please sign-in with your SLSU institutional email</span>
                </div>
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
        <div class="auth-bg-con">
            <img src="{{ asset('assets/img/backgrounds/login_bg_3.png') }}" alt="">
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
