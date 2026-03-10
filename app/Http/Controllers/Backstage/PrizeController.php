<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backstage\Prizes\StoreRequest;
use App\Http\Requests\Backstage\Prizes\UpdateRequest;
use App\Models\Prize;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrizeController extends Controller
{
    public function index(): View
    {
        return view('backstage.prizes.index');
    }

    public function create(): View
    {
        return view('backstage.prizes.create', [
            'prize' => new Prize,
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['campaign_id'] = session('activeCampaign');

        Prize::create($data);

        session()->flash('success', 'The prize has been created!');

        return redirect()->route('backstage.prizes.index');
    }

    public function edit(Prize $prize): View
    {
        return view('backstage.prizes.edit', [
            'prize' => $prize,
        ]);
    }

    public function update(UpdateRequest $request, Prize $prize): RedirectResponse
    {
        $data = $request->validated();
        $data['campaign_id'] = session('activeCampaign');

        $prize->update($data);

        session()->flash('success', 'The prize has been updated!');

        return redirect()->route('backstage.prizes.edit', $prize->id);
    }

    public function destroy(Prize $prize): RedirectResponse
    {
        $prize->delete();

        session()->flash('success', 'The prize has been deleted!');

        return redirect()->route('backstage.prizes.index');
    }
}
