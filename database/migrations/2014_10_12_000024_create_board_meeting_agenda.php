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
            $table->unsignedInteger('board_oob_id')->nullable();
            $table->tinyInteger('status');
            $table->integer('order_no')->nullable();
            $table->unsignedBigInteger('group_proposal_id')->nullable();
            $table->timestamps();
            $table->softdeletes();

            $table->foreign('board_proposal_id')->references('id')->on('proposals')->onDelete('cascade');
            $table->foreign('bor_meeting_id')->references('id')->on('bor_meetings')->onDelete('cascade');
            $table->foreign('board_oob_id')->references('id')->on('board_oob')->onDelete('cascade');

            $table->foreign('group_proposal_id')->nullable()->references('id')->on('group_proposals')->onDelete('cascade');
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
