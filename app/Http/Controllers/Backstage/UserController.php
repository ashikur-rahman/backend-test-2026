<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backstage\Users\StoreRequest;
use App\Http\Requests\Backstage\Users\UpdateRequest;
use App\Mail\Backstage\Users\WelcomeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('backstage.users.index');
    }

    public function create(): View
    {
        return view('backstage.users.create', [
            'user' => new User,
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $password = Str::random(10);
        $data['password'] = bcrypt($password);

        $user = User::create($data);

        $user->update([
            'ott' => encrypt($user->id),
        ]);

        Mail::to($user)->queue(new WelcomeMail($user));

        session()->flash('success', 'The user has been created!');

        return redirect()->route('backstage.users.index');
    }

    public function edit(User $user): View
    {
        return view('backstage.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            if (auth()->user()->id !== $user->id) {
                unset($data['password']);
            } else {
                $data['password'] = bcrypt($data['password']);
            }
        }

        $user->update($data);

        session()->flash('success', 'The user details have been saved!');

        return redirect()->route('backstage.users.edit', $user->id);
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->forceDelete();

        session()->flash('success', 'The user has been removed!');

        return redirect()->route('backstage.users.index');
    }
}
