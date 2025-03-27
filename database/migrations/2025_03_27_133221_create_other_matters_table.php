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
        Schema::create('other_matters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_of_business_id')->nullable();
            $table->unsignedBigInteger('proposal_id')->nullable();

            $table->foreign('order_of_business_id')->references('id')->on('order_of_businesses')->onDelete('cascade');
            $table->foreign('proposal_id')->references('id')->on('proposals')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_matters');
    }
};
