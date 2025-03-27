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
        Schema::create('university_oob', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('university_council_meeting_id')->nullable();
            $table->foreign('university_council_meeting_id')->nullable()->references('id')->on('university_council_meetings')->onDelete('cascade');
            $table->tinyInteger('status');
            $table->text('preliminaries');
            $table->timestamps();
            $table->softdeletes();
            $table->text('previous_minutes')->nullable();
            $table->text('previous_attendance')->nullable();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_oob');
    }
};
