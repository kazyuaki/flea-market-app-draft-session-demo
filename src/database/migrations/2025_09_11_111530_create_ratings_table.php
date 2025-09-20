<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete(); 
            $table->foreignId('rater_id')->constrained('users')->cascadeOnDelete(); 
            $table->foreignId('ratee_id')->constrained('users')->cascadeOnDelete(); 
            $table->unsignedTinyInteger('score'); 
            $table->timestamps();

            $table->unique(['transaction_id', 'rater_id', 'ratee_id']);

            $table->index(['ratee_id', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
