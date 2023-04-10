<?php

namespace App\Actions\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login
{

    use AsAction;

    public function rules(): array
    {
        return [
            'email'     => ['required', 'string', 'email'],
            'password'  => ['required', 'string'],
        ];
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(string $email, string $ipAddress): string
    {
        return Str::transliterate(Str::lower($email) . '|' . $ipAddress);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(ActionRequest $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request->input('email'), $request->ip()), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request->input('email'), $request->ip()));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Validate request and rate limit
     *
     * @param Validator $validator
     * @param ActionRequest $request
     * @return void
     */
    public function withValidator(Validator $validator, ActionRequest $request)
    {
        $validator->after(function (Validator $validator) use ($request) {
            $this->ensureIsNotRateLimited($request);
        });
    }

    /**
     * Handle login
     *
     * @param string $email
     * @param string $password
     * @param boolean $remember
     * @param string|null $ipAddress
     * @return void
     */
    public function handle(string $email, string $password, bool $remember = false, string $ipAddress = null,)
    {
        if (!Auth::attempt([
            'email'     => $email,
            'password'  => $password
        ], $remember)) {
            RateLimiter::hit($this->throttleKey($email, $ipAddress));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($email, $ipAddress));
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle(
            $request->get('email'),
            $request->get('password'),
            $request->boolean('remember'),
            $request->ip(),
        );

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
