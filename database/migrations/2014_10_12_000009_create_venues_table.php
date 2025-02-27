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
        Schema::create('venues', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement(); 
            $table->string('name', 255);
            $table->unsignedBigInteger('campus_id');
            

            $table->foreign('campus_id')->references('id')->on('campus');

            $table->timestamps();
            $table->softdeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
