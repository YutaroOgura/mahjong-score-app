<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameResult;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * 対局一覧を取得
     */
    public function index()
    {
        $games = Game::with(['results.player' => function ($query) {
            $query->select('players.id', 'players.name');
        }])
        ->orderBy('played_at', 'desc')
        ->paginate(20);

        return response()->json($games);
    }

    /**
     * 新規対局を作成
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'played_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'game_type' => 'required|in:east_only,east_south',
            'uma_1st' => 'required|integer',
            'uma_2nd' => 'required|integer',
            'uma_3rd' => 'required|integer',
            'uma_4th' => 'required|integer',
            'starting_points' => 'required|integer',
            'note' => 'nullable|string',
            'results' => 'required|array|size:4',
            'results.*.player_id' => 'required|exists:players,id',
            'results.*.seat' => 'required|integer|between:0,3',
            'results.*.points' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            // 対局を作成
            $game = Game::create([
                'played_at' => $validated['played_at'],
                'location' => $validated['location'],
                'game_type' => $validated['game_type'],
                'uma_1st' => $validated['uma_1st'],
                'uma_2nd' => $validated['uma_2nd'],
                'uma_3rd' => $validated['uma_3rd'],
                'uma_4th' => $validated['uma_4th'],
                'starting_points' => $validated['starting_points'],
                'note' => $validated['note'],
            ]);

            // 点数でソートして順位を決定
            $results = collect($validated['results'])->sortByDesc('points')->values();
            
            // 対局結果を作成
            foreach ($results as $index => $result) {
                $umaKey = 'uma_' . ($index + 1) . 'st';
                $uma = $validated[$umaKey];
                $score = $result['points'] - $validated['starting_points'] + $uma * 1000;
                
                GameResult::create([
                    'game_id' => $game->id,
                    'player_id' => $result['player_id'],
                    'seat' => $result['seat'],
                    'rank' => $index + 1,
                    'points' => $result['points'],
                    'score' => $score,
                ]);

                // プレイヤーの統計情報を更新
                $player = Player::find($result['player_id']);
                $player->total_games++;
                $player->total_points += $score;
                $player->average_rank = ($player->average_rank * ($player->total_games - 1) + ($index + 1)) / $player->total_games;
                $player->save();
            }

            DB::commit();
            return response()->json($game->load('results.player'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create game'], 500);
        }
    }

    /**
     * 対局の詳細情報を取得
     */
    public function show(Game $game)
    {
        $game->load(['results.player' => function ($query) {
            $query->select('id', 'name', 'nickname');
        }]);

        return response()->json($game);
    }

    /**
     * 対局情報を更新
     */
    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'played_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'game_type' => 'required|in:east_only,east_south',
            'note' => 'nullable|string',
        ]);

        $game->update($validated);

        return response()->json($game);
    }

    /**
     * 対局を削除
     */
    public function destroy(Game $game)
    {
        DB::beginTransaction();
        try {
            // プレイヤーの統計情報を更新
            foreach ($game->results as $result) {
                $player = $result->player;
                $player->total_games--;
                $player->total_points -= $result->score;
                if ($player->total_games > 0) {
                    $player->average_rank = ($player->average_rank * ($player->total_games + 1) - $result->rank) / $player->total_games;
                } else {
                    $player->average_rank = 0;
                }
                $player->save();
            }

            $game->delete();
            DB::commit();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete game'], 500);
        }
    }
} 