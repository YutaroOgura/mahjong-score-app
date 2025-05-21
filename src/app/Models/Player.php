<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nickname',
        'total_games',
        'total_points',
        'average_rank',
    ];

    protected $casts = [
        'total_games' => 'integer',
        'total_points' => 'integer',
        'average_rank' => 'float',
    ];

    /**
     * このプレイヤーが参加した対局結果を取得
     */
    public function gameResults()
    {
        return $this->hasMany(GameResult::class);
    }

    /**
     * このプレイヤーが参加した対局を取得
     */
    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_results')
            ->withPivot(['seat', 'rank', 'points', 'score'])
            ->withTimestamps();
    }
} 