<?php
use Illuminate\Support\Facades\Auth;

if (!function_exists('getUserRole')) {
    function getUserRole()
    {
        if (Auth::check()) {
            return config('user_roles.role')[Auth::user()->role] ?? 'proponent';
        }
        return; 
    }
}