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
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('local_council_meeting_id')->nullable();
            $table->unsignedInteger('local_proposal_id')->nullable();
            $table->unsignedInteger('local_oob_id')->nullable();
            $table->tinyInteger('status');
            $table->tinyInteger('order_no');
            $table->timestamps();
            $table->softdeletes();

            $table->foreign('local_council_meeting_id')->nullable()->references('id')->on('local_council_meetings')->onDelete('cascade');
            $table->foreign('local_proposal_id')->nullable()->references('id')->on('proposals')->onDelete('cascade');
            $table->foreign('local_oob_id')->nullable()->references('id')->on('local_oob')->onDelete('cascade');
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
