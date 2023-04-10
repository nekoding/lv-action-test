<?php

namespace App\Actions\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ConfirmPassword
{

    use AsAction;

    public function handle(string $email, string $password): bool
    {
        return Auth::guard('web')->validate([
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        if (!$this->handle($request->user()->email, $request->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
