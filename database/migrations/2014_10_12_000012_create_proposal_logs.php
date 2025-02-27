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
        Schema::create('proposal_logs', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status');
            $table->text('comments')->nullable();
            $table->string('file_id', 255);
            $table->unsignedInteger('proposal_id');


            $table->foreign('proposal_id')->nullable()->references('id')->on('proposals')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_logs');
    }
};
