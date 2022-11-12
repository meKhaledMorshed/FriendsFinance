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
        Schema::create('nominees', function (Blueprint $table) {

            $table->id();
            $table->foreignId('accountNum')->constrained('accounts', 'accountNumber')->onDelete('cascade');

            $table->string('name', 255);
            $table->date('birthday');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('photo', 255)->unique()->nullable();

            $table->enum('relation', ['Father', 'Mother', 'Brother', 'Sister', 'Wife', 'Other']);
            $table->unsignedFloat('percentage', 3)->default(100);

            $table->string('nid', 20)->nullable();
            $table->string('passport', 20)->nullable();

            $table->string('email', 100)->nullable();
            $table->string('mobile', 20)->nullable()->comment('Noninee Mobile Number.');
            $table->string('address', 255)->nullable();

            $table->string('remarks', 255)->nullable();
            $table->boolean('isActive')->nullable()->default(0);

            $table->foreignId('insertedBy')->constrained('admins');
            $table->timestamp('insertedDate')->useCurrent();
            $table->foreignId('modifiedBy')->nullable()->constrained('admins');
            $table->timestamp('modifiedDate')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('isAuth')->nullable()->comment('null > Pending, 0 > Unauth, 1 > Auth, -1 > Reject,');
            $table->foreignId('authBy')->nullable()->constrained('admins');
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
        Schema::dropIfExists('nominees');
    }
};
