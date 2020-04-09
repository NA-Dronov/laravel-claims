<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id('response_id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('claim_id');

            $table->string('subject', 256);
            $table->text('body');

            $table->timestamps();

            $table->foreign('claim_id')->references('claim_id')->on('claims');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('responses');
    }
}
