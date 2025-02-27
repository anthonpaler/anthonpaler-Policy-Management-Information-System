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
        Schema::create('university_meeting_agenda', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('local_meeting_agenda_id')->nullable();
            $table->unsignedInteger('university_proposal_id');
            $table->unsignedInteger('university_meeting_id');




            $table->foreign('local_meeting_agenda_id')->nullable()->references('id')->on('local_meeting_agenda')->onDelete('cascade');
            $table->foreign('university_proposal_id')->references('id')->on('proposals')->onDelete('cascade');
            $table->foreign('university_meeting_id')->references('id')->on('university_council_meetings')->onDelete('cascade');

            $table->tinyInteger('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_meeting_agenda');
    }
};
