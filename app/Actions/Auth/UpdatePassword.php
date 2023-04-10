<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePassword
{

    use AsAction;

    public function rules(): array
    {
        return [
            'current_password'  => ['required', 'current_password'],
            'password'          => ['required', Password::defaults(), 'confirmed'],
        ];
    }

    public function withValidator(Validator $validator, ActionRequest $request): void
    {
        $validator->after(function (Validator $validator) use ($request) {
            $request->validateWithBag('updatePassword', $this->rules());
        });
    }

    public function handle(User $user, string $newPassword)
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function asController(ActionRequest $request)
    {
        $validated = $request->validated();

        $this->handle($request->user(), $validated['password']);

        return back()->with('status', 'password-updated');
    }
}
