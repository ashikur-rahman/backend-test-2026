<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Prize;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function flip(Request $request)
    {
        // Validate request
        $request->validate([
            'gameId' => 'required|integer|exists:games,id',
            'tileIndex' => 'required|integer|min:0|max:24',
        ]);

        $game = Game::findOrFail($request->gameId);

        // Game already finished
        if ($game->finished_at) {
            return response()->json([
                'message' => 'Game already finished'
            ]);
        }

        $tileIndex = $request->tileIndex;
        $revealed = $game->revealed_tiles ?? [];

        // Prevent duplicate click
        if (collect($revealed)->contains('index', $tileIndex)) {
            $existing = collect($revealed)->firstWhere('index', $tileIndex);
            return response()->json([
                'tileImage' => '/' . ltrim($existing['image'], '/')
            ]);
        }

        // Get available prizes (segment + campaign + date + daily limit)
        $baseQuery = Prize::where('campaign_id', $game->campaign_id)
            ->where('segment', $game->segment)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->whereRaw("
                (daily_limit = 0 OR
                (SELECT COUNT(*) FROM games
                WHERE games.prize_id = prizes.id
                AND DATE(finished_at) = CURDATE()) < daily_limit)
            ");

        // Check if any prizes are available
        if ($baseQuery->count() === 0) {
            return response()->json([
                'message' => 'No prizes available at this time. Please try again later.'
            ], 503);
        }

        // Decide if this game should win (only once)
        if (!isset($game->should_win)) {
            $game->should_win = rand(1, 100) <= 30; // 30% win rate
            $game->save();
        }

        // Illusion logic with control
        if ($game->moves < 2) {
            // Early stage random
            $prize = $baseQuery
                ->orderByRaw('-LOG(RAND()) / weight')
                ->first();
        } else {
            $existingPrizeIds = collect($revealed)->pluck('prize_id')->unique()->values();

            if ($game->should_win && $existingPrizeIds->isNotEmpty()) {
                // WIN PATH - bias toward match
                $prize = (clone $baseQuery)
                    ->whereIn('id', $existingPrizeIds)
                    ->orderByRaw('-LOG(RAND()) / weight')
                    ->first();

                // Fallback if existing prizes are no longer available
                if (!$prize) {
                    $prize = $baseQuery->orderByRaw('-LOG(RAND()) / weight')->first();
                }
            } else {
                // LOSE PATH - avoid match
                $prize = (clone $baseQuery)
                    ->whereNotIn('id', $existingPrizeIds)
                    ->orderByRaw('-LOG(RAND()) / weight')
                    ->first();

                // Fallback if all other prizes are exhausted
                if (!$prize) {
                    $prize = $baseQuery->orderByRaw('-LOG(RAND()) / weight')->first();
                }
            }
        }

        // Save tile
        $tile = [
            'index' => $tileIndex,
            'image' => $prize->image,
            'prize_id' => $prize->id
        ];

        $revealed[] = $tile;

        // Count matches
        $matchCount = collect($revealed)
            ->where('prize_id', $prize->id)
            ->count();

        $game->moves += 1;

        // WIN CONDITION
        if ($matchCount >= 3) {
            $game->prize_id = $prize->id;
            $game->finished_at = now();
        }

        // Save game state
        $game->revealed_tiles = $revealed;
        $game->save();

        // Response format
        if ($matchCount >= 3) {
            return response()->json([
                'tileImage' => '/' . ltrim($prize->image, '/'),
                'message' => 'You won a prize!'
            ]);
        }

        return response()->json([
            'tileImage' => '/' . ltrim($prize->image, '/')
        ]);
    }
}
