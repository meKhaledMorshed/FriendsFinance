<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('email', 100)->unique()->comment('Users email address.');
            $table->string('mobile', 20)->nullable()->comment('Users Mobile Number.');
            $table->string('ccc', 6)->nullable()->comment("Country Calling Code");
            $table->string('password', 255);
            $table->boolean('twoFA')->default(0)->comment('Two-factor authentication. 0 > off, 1 > on');
            $table->boolean('isAdmin')->default(0);
            $table->boolean('isActive')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
