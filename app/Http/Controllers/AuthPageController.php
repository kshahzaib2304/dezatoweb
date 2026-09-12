<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Auth screens (UI-first). Wire Laravel Auth / Socialite later.
 */
class AuthPageController extends Controller
{
    public function login(): View
    {
        return view('auth.login', [
            'title' => 'Sign in | Dezato Cake House',
            'metaDescription' => 'Sign in to your Dezato Cake House account.',
        ]);
    }

    public function register(): View
    {
        return view('auth.register', [
            'title' => 'Create account | Dezato Cake House',
            'metaDescription' => 'Create a Dezato Cake House account for faster checkout and saved addresses.',
        ]);
    }

    public function forgot(): View
    {
        return view('auth.forgot-password', [
            'title' => 'Forgot password | Dezato Cake House',
            'metaDescription' => 'Reset your Dezato Cake House password.',
        ]);
    }

    public function reset(): View
    {
        return view('auth.reset-password', [
            'title' => 'Reset password | Dezato Cake House',
            'metaDescription' => 'Choose a new password for your Dezato account.',
        ]);
    }

    public function stub(Request $request): RedirectResponse
    {
        return back()->with(
            'status',
            'UI ready — authentication will be connected in the next backend phase.'
        );
    }
}
