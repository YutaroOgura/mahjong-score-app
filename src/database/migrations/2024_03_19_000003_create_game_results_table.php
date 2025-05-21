<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('game_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->integer('seat')->comment('起家からの位置（0:起家, 1:南家, 2:西家, 3:北家）');
            $table->integer('rank')->comment('順位（1-4）');
            $table->integer('points')->comment('最終点数');
            $table->integer('score')->comment('ウマを含む最終得点');
            $table->timestamps();
            $table->unique(['game_id', 'player_id']);
            $table->unique(['game_id', 'seat']);
            $table->unique(['game_id', 'rank']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_results');
    }
}; 