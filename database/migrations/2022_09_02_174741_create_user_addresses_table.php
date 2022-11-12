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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uid')->constrained('users')->onDelete('cascade');

            $table->string('house', 255)->nullable()->comment('House Name, Number etc. ');
            $table->string('area', 255)->nullable()->comment('Area,Village, Block, Road, Ward rtc.');
            $table->string('postOffice', 255)->comment('Post office with post code.');
            $table->string('policeStation', 255);
            $table->string('district', 255);
            $table->string('country', 255);

            $table->string('remarks', 255)->nullable()->comment('Remarks about the address if any');

            $table->enum('type', ['Present', 'Permanent', 'Temporary'])->comment("Users address type");

            $table->boolean('isActive')->nullable()->default(0);

            $table->foreignId('insertedBy')->constrained('users');
            $table->timestamp('insertedDate')->useCurrent();

            $table->foreignId('modifiedBy')->nullable();
            $table->timestamp('modifiedDate')->useCurrent()->useCurrentOnUpdate();

            $table->boolean('isAuth')->nullable()->comment('null > Pending, 0 > Unauth, 1 > Auth, -1 > Reject,');
            $table->foreignId('authBy')->nullable();
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
        Schema::dropIfExists('user_addresses');
    }
};
