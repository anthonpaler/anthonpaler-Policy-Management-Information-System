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
        Schema::create('local_oob', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('local_council_meeting_id')->nullable();
            $table->foreign('local_council_meeting_id')->nullable()->references('id')->on('local_council_meetings')->onDelete('cascade');
            $table->tinyInteger('status');
            $table->text('preliminaries');
            $table->timestamps();
            $table->softdeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_oob');
    }
};
