<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->dateTime('played_at');
            $table->string('location')->nullable();
            $table->enum('game_type', ['east_only', 'east_south'])->default('east_south');
            $table->integer('uma_1st')->default(30);
            $table->integer('uma_2nd')->default(10);
            $table->integer('uma_3rd')->default(-10);
            $table->integer('uma_4th')->default(-30);
            $table->integer('starting_points')->default(25000);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
}; 