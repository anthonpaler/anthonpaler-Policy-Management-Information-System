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
        Schema::create('local_meeting_agenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_council_meeting_id')->constrained('local_council_meetings')->onDelete('cascade');
            $table->foreignId('local_proposal_id')->constrained('proposals')->onDelete('cascade');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_meeting_agenda');
    }
};
