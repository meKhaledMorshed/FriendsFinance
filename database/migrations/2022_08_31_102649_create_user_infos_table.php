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
        Schema::create('user_infos', function (Blueprint $table) {

            $table->id();
            $table->foreignId('uid')->constrained('users')->onDelete('cascade');

            $table->string('name', 100)->comment("Users full name.");
            $table->date('birthday');
            $table->enum('gender', ['Male', 'Female', 'Third Gender', 'Other']);
            $table->string('photo', 255)->unique()->nullable();
            $table->string('signature', 255)->unique()->nullable();

            $table->string('mother', 100)->nullable();
            $table->string('father', 100)->nullable();
            $table->string('spouse', 100)->nullable();

            $table->string('profession', 100)->nullable();

            $table->longText('remarks')->nullable();

            $table->foreignId('insertedBy')->constrained('users');
            $table->timestamp('insertedDate')->useCurrent();

            $table->foreignId('modifiedBy')->nullable()->constrained('users');
            $table->timestamp('modifiedDate')->useCurrent()->useCurrentOnUpdate();

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
        Schema::dropIfExists('user_infos');
    }
};
