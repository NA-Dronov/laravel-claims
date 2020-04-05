<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id('claim_id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('manager_id')->default(0);

            $table->string('subject', 256);
            $table->text('body');
            $table->char('status', 1)->default('O');
            $table->boolean('new_response');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->index('status');
        });

        Schema::create('viewed_claims', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('claim_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claims');
    }
}
