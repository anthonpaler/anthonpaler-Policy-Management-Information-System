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
        Schema::create('proposals', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->text('title');
            $table->integer('type');
            $table->integer('sub_type')->nullable();
            $table->tinyInteger('action');
            $table->tinyInteger('status');
            $table->foreignId('campus_id')->constrained('campus')->onDelete('cascade');
            $table->timestamps();
            $table->softdeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
