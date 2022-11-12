<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uid')->constrained('users');
            $table->foreignId('branchID')->constrained('branches');
            $table->foreignId('catID')->constrained('account_categories');

            $table->string('accountName', 255);
            $table->unsignedBigInteger('accountNumber')->unique();

            $table->string('remarks', 255)->nullable();

            $table->boolean('isActive')->nullable()->default(0);

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
        Schema::dropIfExists('accounts');
    }
};
