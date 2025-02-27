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
        Schema::create('university_council_meetings', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();            $table->dateTime('submission_start');
            $table->dateTime('submission_end');
            $table->dateTime('meeting_date_time');
            $table->string('modality', 25)->nullable();
            $table->tinyInteger('quarter');
            $table->integer('year');
            $table->tinyInteger('council_type');
            $table->tinyInteger('mode_if_online')->nullable();
            $table->text('link')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('venue')->nullable();
            $table->foreign('venue')->references('id')->on('venues');
            $table->tinyInteger('status')->default(0);
            $table->foreignId('creator_id')->constrained('employees')->onDelete('cascade');
            $table->timestamps();
            $table->softdeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_council_meetings');
    }
};
