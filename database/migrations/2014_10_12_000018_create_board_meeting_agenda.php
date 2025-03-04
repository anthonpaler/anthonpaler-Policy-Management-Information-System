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
        Schema::create('board_meeting_agenda', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('board_proposal_id');
            $table->unsignedInteger('bor_meeting_id');

            $table->foreign('board_proposal_id')->references('id')->on('proposals')->onDelete('cascade');
            $table->foreign('bor_meeting_id')->references('id')->on('bor_meetings')->onDelete('cascade');
            $table->tinyInteger('status');
            $table->timestamps();
            $table->softdeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_meeting_agenda');
    }
};
