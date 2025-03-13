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
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity');
            $table->string('zoom_meeting_id')->nullable();
            $table->string('zoom_meeting_url')->nullable();
            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
