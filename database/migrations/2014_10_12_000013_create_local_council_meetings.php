<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('local_council_meetings', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();            
            $table->dateTime('submission_start');
            $table->dateTime('submission_end');
            $table->dateTime('meeting_date_time')->nullable();
            $table->string('modality', 25)->nullable();
            $table->tinyInteger('quarter');
            $table->integer('year');
            $table->tinyInteger('council_type');
            $table->tinyInteger('mode_if_online')->nullable();
            $table->unsignedBigInteger('campus_id');
            $table->text('link')->nullable();
            $table->text('description')->nullable();
            $table->string('venue', 150)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->foreignId('creator_id')->constrained('employees')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campus');
            $table->timestamps();
            $table->softdeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_council_meetings');
    }
};
