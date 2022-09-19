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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30);
            $table->string('uid', 128)->unique(); // Firebase UID

            // Mutable unique fields
            $table->string('email', 100)->unique();
            $table->string('phone', 15)->nullable(); // Intl phone number

            $table->date('dob')->nullable();

            $table->enum('role', ['admin', 'user'])->default('user');

            // Managed by the framework
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
