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
        Schema::create('group_proposal_files', function (Blueprint $table) {
          $table->id();
          $table->text('file_name');
          $table->text('file');
          $table->unsignedBigInteger('group_proposal_id');
          $table->tinyInteger('order_no')->nullable();
          $table->timestamps();
          $table->softdeletes();

          $table->foreign('group_proposal_id')->nullable()->references('id')->on('group_proposals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_proposal_files');
    }
};
