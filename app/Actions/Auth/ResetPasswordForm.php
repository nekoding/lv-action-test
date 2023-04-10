<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\View\View;
use Lorisleiva\Actions\ActionRequest;

class ResetPasswordForm
{

    use AsAction;

    public function handle()
    {
    }

    public function asController(string $token): string
    {
        return $token;
    }

    public function htmlResponse(string $token, ActionRequest $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }
}
