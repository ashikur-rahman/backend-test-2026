<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\View\View;
use App\Models\Game;

class FrontendController extends Controller
{
    public function loadCampaign(Campaign $campaign): View
    {
        $account = request('a');
        $segment = request('segment', 'low');

        // 🔥 Campaign validity
        if (now() < $campaign->starts_at) {
            return view('frontend.index', [
                'config' => json_encode(["message" => "Campaign not started"])
            ]);
        }

        if (now() > $campaign->ends_at) {
             return view('frontend.index', [
                'config' => json_encode([
                    "message" => "Campaign has ended"
                ])
            ]);
        }

        // 🔥 Find unfinished game
        $game = Game::where('campaign_id', $campaign->id)
            ->where('account', $account)
            ->whereNull('finished_at')
            ->latest()
            ->first();

        if (!$game) {
            $game = Game::create([
                'campaign_id' => $campaign->id,
                'account' => $account,
                'segment' => $segment,
                'revealed_tiles' => [],
                'moves' => 0
            ]);
        }

        $config = [
            "apiPath" => "/api/flip",
            "gameId" => $game->id,
            "reveledTiles" => $game->revealed_tiles ?? [],
        ];

        return view('frontend.index', ['config' => json_encode($config)]);
    }

    public function placeholder(): View
    {
        return view('frontend.placeholder');
    }


}
