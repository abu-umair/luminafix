<?php

use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Project::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();

            $table->enum('role', ['member', 'leader', 'ba', 'da', 'ld'])->default('member');

            // User can have multiple roles in a single project but cannot have
            // same role multiple times in single project
            // Example: There could not be two entries of a user in a project
            //          having the `ba` role, there must be only one!
            $table->unique(['project_id', 'user_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_members');
    }
};
