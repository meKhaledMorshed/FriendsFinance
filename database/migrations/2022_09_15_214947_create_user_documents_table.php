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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uid')->constrained('users')->onDelete('cascade');

            $table->string('docName', 255)->nullable()->comment('Document Name');
            $table->string('docNumber', 50)->nullable()->comment('Document Number');
            $table->string('document', 255)->unique()->nullable()->comment('File name.');

            $table->enum('type', ['NID', 'PASSPORT', 'BRC', 'DL', 'OTHERS'])->default('OTHERS')->comment("NID, PASSPORT, BRC, DL,  OTHERS");

            $table->longText('remarks')->nullable();

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
        Schema::dropIfExists('user_documents');
    }
};
