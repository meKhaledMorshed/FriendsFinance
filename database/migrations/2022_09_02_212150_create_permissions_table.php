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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adminID')->constrained('admins');
            $table->boolean('readPermit')->default(1);
            $table->boolean('writePermit')->default(0);
            $table->boolean('editPermit')->default(0);
            $table->boolean('deletePermit')->default(0);
            $table->foreignId('permitBy')->constrained('users');
            $table->timestamp('permitDate')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
