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

    public function withValidator(Validator $validator, ActionRequest $request): void
    {
        $validator->after(function (Validator $validator) use ($request) {
            $request->validateWithBag('userDeletion', $this->rules());
        });
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $this->handle($request->user());

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
