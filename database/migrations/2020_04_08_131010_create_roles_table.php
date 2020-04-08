<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('role_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('abilities', function (Blueprint $table) {
            $table->bigIncrements('ability_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });


        Schema::create('ability_role', function (Blueprint $table) {
            $table->primary(['ability_id', 'role_id']);

            $table->unsignedBigInteger('ability_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('ability_id')
                ->references('ability_id')
                ->on('abilities')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('role_id')
                ->on('roles')
                ->onDelete('cascade');
        });


        Schema::create('role_user', function (Blueprint $table) {
            $table->primary(['role_id', 'user_id']);

            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('role_id')
                ->references('role_id')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ability_role');
        Schema::dropIfExists('abilities');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
}
