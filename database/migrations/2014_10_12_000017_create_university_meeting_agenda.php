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
            $table->id();
            $table->foreignId('local_meeting_agenda_id')->constrained('local_meeting_agenda')->onDelete('cascade');
            $table->foreignId('university_proposal_id')->constrained('proposals')->onDelete('cascade');
            $table->foreignId('university_meeting_id')->constrained('university_council_meetings')->onDelete('cascade');

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
