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
        Schema::create('proposal_files', function (Blueprint $table) {
            $table->id();
            $table->text('file');
            $table->tinyInteger('version');
            $table->tinyInteger('file_status');
            $table->tinyInteger('is_active');
            $table->tinyInteger('file_reference_id')->nullable();
            $table->timestamps();
            $table->foreignId('proposals_id')->constrained('proposals')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_files');
    }
};
