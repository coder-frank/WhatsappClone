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
        Schema::create('message_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id'); // the reply message
            $table->unsignedBigInteger('replied_to_id'); // original message being replied to
            $table->timestamps();
        
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreign('replied_to_id')->references('id')->on('messages')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_replies');
    }
};
