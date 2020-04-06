<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Three statusese by default:
         * 1. O - open. Claim created but not assigned
         * 2. P - processing. Claim assigned to manager
         * 3. C - closed. Claim closed by manager or user
         */
        Schema::create('claim_statuses', function (Blueprint $table) {
            $table->id('claim_statuses_id');
            $table->char('code', 1)->unique();
            $table->string('status', 256);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim_statuses');
    }
}
