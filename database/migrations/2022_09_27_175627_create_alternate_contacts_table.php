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
        Schema::create('alternate_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uid')->comment("User's ID")->constrained('users')->onDelete('cascade');

            $table->string('contact', 100)->comment('Email address, Mobile number or other');
            $table->enum('type', ['email', 'mobile', 'other'])->default('other')->comment('enum: email, mobile, other');

            $table->boolean('isActive')->nullable()->default(0)->comment('0 > Inactive, 1 > Active');

            $table->foreignId('insertedBy')->constrained('users')->onDelete('cascade');
            $table->timestamp('insertedDate')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alternate_contacts');
    }
};
