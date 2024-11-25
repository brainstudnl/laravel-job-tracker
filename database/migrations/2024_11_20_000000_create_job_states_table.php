<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('job_states', function (Blueprint $table) {
            $table->id();
            $table->char('identifier', 36);
            $table->string('job_id')->index()->nullable();

            $table->enum('status', ['pending', 'processing', 'success', 'failed'])->default('pending');
            $table->string('queue')->default('default');
            $table->morphs('subject');
            $table->string('job_data')->nullable();
            $table->longText('exception')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('job_states');
    }
};
