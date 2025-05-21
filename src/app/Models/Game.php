<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'played_at',
        'location',
        'game_type',
        'uma_1st',
        'uma_2nd',
        'uma_3rd',
        'uma_4th',
        'starting_points',
        'note',
    ];

    protected $casts = [
        'played_at' => 'datetime',
        'uma_1st' => 'integer',
        'uma_2nd' => 'integer',
        'uma_3rd' => 'integer',
        'uma_4th' => 'integer',
        'starting_points' => 'integer',
    ];

    /**
     * この対局の結果を取得
     */
    public function results()
    {
        return $this->hasMany(GameResult::class);
    }

    /**
     * この対局に参加したプレイヤーを取得
     */
    public function players()
    {
        return $this->belongsToMany(Player::class, 'game_results')
            ->withPivot(['seat', 'rank', 'points', 'score'])
            ->withTimestamps();
    }
} 