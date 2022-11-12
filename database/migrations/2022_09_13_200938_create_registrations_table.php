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
        Schema::create('registrations', function (Blueprint $table) {

            $table->id();
            $table->string('name', 100);
            $table->date('birthday');
            $table->enum('gender', ['Male', 'Female', 'Third Gender', 'Other']);

            $table->string('email', 100)->unique();
            $table->string('mobile', 20)->nullable()->comment('Users Mobile Number.');
            $table->string('ccc', 6)->nullable()->comment("Country Calling Code");

            $table->string('photo', 255)->unique()->nullable();

            $table->string('token', 100);
            $table->unsignedBigInteger('otpExpiryTime')->comment('OTP expiry timestamp in number (seconds)');
            $table->boolean('validity')->default(1);

            $table->timestamp('regDate')->useCurrent()->comment('Registration Date');

            $table->boolean('isAuth')->nullable()->comment('null > Pending, 0 > Unauth, 1 > Auth, -1 > Reject,');
            $table->foreignId('authBy')->nullable()->constrained('users');
            $table->timestamp('authDate')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};
