<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\SocialAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        $this->guardProvider($provider);

        SocialAuth::apply();

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->guardProvider($provider);

        SocialAuth::apply();

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Social sign-in was cancelled or failed. Please try again.']);
        }

        $email = strtolower(trim((string) $socialUser->getEmail()));

        if ($email === '') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your '.$provider.' account did not share an email. Please use email sign-in or another provider.']);
        }

        $user = User::query()
            ->where('provider', $provider)
            ->where('provider_id', (string) $socialUser->getId())
            ->first();

        if ($user === null) {
            $user = User::query()->where('email', $email)->first();
        }

        if ($user === null) {
            $user = User::query()->create([
                'name' => trim((string) ($socialUser->getName() ?: $socialUser->getNickname())) ?: 'Customer',
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Str::password(32),
                'role' => User::ROLE_CUSTOMER,
                'provider' => $provider,
                'provider_id' => (string) $socialUser->getId(),
            ]);
        } else {
            $user->forceFill([
                'provider' => $provider,
                'provider_id' => (string) $socialUser->getId(),
                'email_verified_at' => $user->email_verified_at ?? now(),
                'name' => $user->name ?: (trim((string) $socialUser->getName()) ?: $user->name),
            ])->save();
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()
            ->intended(route('account.profile'))
            ->with('status', 'Signed in with '.Str::title($provider).'.');
    }

    private function guardProvider(string $provider): void
    {
        abort_unless(SocialAuth::isEnabled($provider), 404);
    }
}
