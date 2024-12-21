<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();

            $table->string('owner');

            $table->integer('owner_id');

            $table->foreignId('project_id')->constrained('projects')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('project_name');

            $table->foreignId('user_id')->constrained('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('user_name');

            $table->foreignId('task_id')->constrained('tasks')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('task_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
