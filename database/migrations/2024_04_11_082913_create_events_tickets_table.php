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
        Schema::create('events_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id');
            $table->integer('ticket_id');
            $table->string('ticket_no')->unique();
            $table->decimal('price', 10, 2);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_tickets');
    }
};
