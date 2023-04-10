<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SendEmailVerification
{

    use AsAction;

    public function handle(User $user)
    {
        $user->sendEmailVerificationNotification();
    }

    public function getControllerMiddleware(): array
    {
        return ['signed', 'throttle:6,1'];
    }

    public function asController(ActionRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $this->handle($request->user());

        return back()->with('status', 'verification-link-sent');
    }
}
