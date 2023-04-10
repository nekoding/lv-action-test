<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAccount
{

    use AsAction;

    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }

    public function handle(User $user)
    {
        Auth::logout();
        $user->delete();
    }

    public function getValidationErrorBag(): string
    {
        return 'userDeletion';
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->user());

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
