<?php

namespace App\Actions\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ForgotPassword
{

    use AsAction;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email']
        ];
    }

    public function handle(array $credentials): string
    {
        return Password::sendResetLink($credentials);
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $status = $this->handle($request->only('email'));

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
