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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->unsignedBigInteger('budget')->default(0);
            $table->unsignedTinyInteger('workload')->default(0);

            // Controlled from app
            $table->unsignedTinyInteger('concept')->default(0);
            $table->unsignedTinyInteger('development')->default(0);
            $table->unsignedTinyInteger('documentation')->default(0);
            $table->unsignedTinyInteger('commissioning')->default(0);

            $table->mediumText('phase');
            $table->enum('status', array_keys(config('lumina.project.status')));

            $table
                ->foreignId('leader_id')
                ->constrained()
                ->references('id')
                ->on('users');

            $table->timestamps();

            $table->fulltext('phase');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
