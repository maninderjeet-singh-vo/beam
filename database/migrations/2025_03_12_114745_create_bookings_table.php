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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('creater_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('slot_id');
            $table->enum('payment_status', ['pending', 'paid']);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('creater_id')->references('id')->on('users');
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('slot_id')->references('id')->on('slots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
