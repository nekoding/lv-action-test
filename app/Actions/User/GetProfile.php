<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\View\View;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetProfile
{

    use AsAction;

    public function asController(ActionRequest $request)
    {
        return $request->user();
    }

    public function htmlResponse(User $user): View
    {
        return view('profile.edit', compact('user'));
    }
}
