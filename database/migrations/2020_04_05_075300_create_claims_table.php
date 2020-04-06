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
            $table->char('status', 1);

            $table->string('subject', 256);
            $table->text('body');

            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('status')->references('code')->on('claim_statuses');
        });

        /**
         * There are two relations type between users and claims:
         * 1. V - determines if claim was viewed by specific manager
         * 2. R - shows if claim has new responses for client\manager. 
         */
        Schema::create('claim_user_relations', function (Blueprint $table) {
            $table->id('relation_id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('claim_id');
            $table->char('relation_type', 1)->default('V');
            $table->foreign('user_id')->references('user_id')->on('users');
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
        Schema::dropIfExists('claim_user_relations');
        Schema::dropIfExists('claims');
    }
}
