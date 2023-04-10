<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class Register
{

    use AsAction;

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password'  => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function handle(string $name, string $email, string $password)
    {

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        event(new Registered($user));

        Auth::login($user);
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle(
            $request->get('name'),
            $request->get('email'),
            $request->get('password')
        );

        return redirect(RouteServiceProvider::HOME);
    }
}
