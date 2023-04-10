<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyEmailUser
{

    use AsAction;

    public function authorize(ActionRequest $request): bool
    {
        if (!hash_equals((string) $request->user()->getKey(), (string) $request->route('id'))) {
            return false;
        }

        if (!hash_equals(sha1($request->user()->getEmailForVerification()), (string) $request->route('hash'))) {
            return false;
        }

        return true;
    }

    public function handle()
    {
        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::user()->markEmailAsVerified();

            event(new Verified(Auth::user()));
        }
    }

    public function getControllerMiddleware(): array
    {
        return ['throttle:6,1'];
    }


    public function asController(ActionRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }
}
