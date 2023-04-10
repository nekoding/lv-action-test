<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProfile
{

    use AsAction;

    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore(auth()->user()->id)],
        ];
    }

    public function handle(User $user, array $data)
    {
        $user->fill($data);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->user(), $request->validated());
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}
