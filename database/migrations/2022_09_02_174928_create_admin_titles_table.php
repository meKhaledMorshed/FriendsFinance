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
        Schema::create('admin_titles', function (Blueprint $table) {
            $table->id();

            $table->string('title', 100)->unique()->comment('Designation of the post like MD, CEO etc.');
            $table->string('definition', 255)->unique()->nullable()->comment('Full form of title like Managing Director.');
            $table->enum('type', ['Elected', 'Permanent', 'Contractual', 'Temporary', 'Other']);


            $table->string('remarks', 255)->nullable()->comment('Remarks about title if any');

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
        Schema::dropIfExists('admin_titles');
    }
};
