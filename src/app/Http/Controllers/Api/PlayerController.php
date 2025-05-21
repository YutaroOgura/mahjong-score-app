<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    /**
     * プレイヤー一覧を取得
     */
    public function index()
    {
        $players = Player::withCount('games')
            ->orderBy('total_games', 'desc')
            ->get();

        return response()->json($players);
    }

    /**
     * プレイヤーを新規作成
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
        ]);

        $player = Player::create($validated);

        return response()->json($player, 201);
    }

    /**
     * プレイヤーの詳細情報を取得
     */
    public function show(Player $player)
    {
        $player->load(['games' => function ($query) {
            $query->orderBy('played_at', 'desc');
        }]);

        // 直近5戦の成績を取得
        $recentGames = $player->games()
            ->orderBy('played_at', 'desc')
            ->take(5)
            ->get();

        // 順位ごとの回数を集計
        $rankCounts = $player->gameResults()
            ->select('rank', DB::raw('count(*) as count'))
            ->groupBy('rank')
            ->get()
            ->pluck('count', 'rank')
            ->toArray();

        return response()->json([
            'player' => $player,
            'recent_games' => $recentGames,
            'rank_statistics' => $rankCounts,
        ]);
    }

    /**
     * プレイヤー情報を更新
     */
    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
        ]);

        $player->update($validated);

        return response()->json($player);
    }

    /**
     * プレイヤーを削除
     */
    public function destroy(Player $player)
    {
        $player->delete();

        return response()->json(null, 204);
    }

    /**
     * プレイヤーの統計情報を取得
     */
    public function statistics(Player $player)
    {
        $statistics = [
            'total_games' => $player->total_games,
            'total_points' => $player->total_points,
            'average_rank' => $player->average_rank,
            'rank_distribution' => $player->gameResults()
                ->select('rank', DB::raw('count(*) as count'))
                ->groupBy('rank')
                ->get()
                ->pluck('count', 'rank'),
            'monthly_scores' => $player->gameResults()
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(score) as total_score'),
                    DB::raw('COUNT(*) as games_played'),
                    DB::raw('AVG(rank) as average_rank')
                )
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get()
        ];

        return response()->json($statistics);
    }
} 