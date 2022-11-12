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
        Schema::create('select_options', function (Blueprint $table) {
            $table->id();

            $table->string('optionName', 100);
            $table->string('optionValue', 100)->nullable();

            $table->foreignId('parentID')->nullable()->constrained('select_options');
            $table->string('parentValue')->nullable()->constrained('select_options,optionValue');

            $table->string('group', 100)->nullable();

            $table->enum('type', ['Option', 'Group'])->default('Option');

            $table->longText('remarks')->nullable();

            $table->string('insertedBy', 100)->nullable()->constrained('users');

            $table->boolean('isActive')->default(0)->comment(' 0 > Inactive, 1 > Active');

            $table->boolean('isAuth')->nullable()->comment('null > Pending, 0 > Unauth, 1 > Auth, -1 > Reject,');
            $table->string('authBy', 100)->nullable()->constrained('users');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('select_options');
    }
};
