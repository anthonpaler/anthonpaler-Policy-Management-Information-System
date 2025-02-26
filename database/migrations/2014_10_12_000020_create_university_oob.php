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
            $table->id();
            $table->foreignId('university_council_meeting_id')->constrained('university_council_meetings')->onDelete('cascade');
            $table->tinyInteger('status');
            $table->text('preliminaries');

            $table->timestamps();
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
