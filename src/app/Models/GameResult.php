<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'seat',
        'rank',
        'points',
        'score',
    ];

    protected $casts = [
        'seat' => 'integer',
        'rank' => 'integer',
        'points' => 'integer',
        'score' => 'integer',
    ];

    /**
     * この結果が属する対局を取得
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * この結果のプレイヤーを取得
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }
} 