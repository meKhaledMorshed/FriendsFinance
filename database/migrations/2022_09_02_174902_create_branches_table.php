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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            $table->string('branchName', 100)->unique();
            $table->enum('type', ['Core', 'Division', 'Branch', 'SubBranch', 'Reserve', 'Other'])->comment('Branch type');

            $table->string('address')->nullable()->comment('Address of the branch.');

            $table->string('remarks')->nullable();

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
        Schema::dropIfExists('branches');
    }
};
