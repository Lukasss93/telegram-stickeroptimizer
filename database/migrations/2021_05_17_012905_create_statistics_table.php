<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('chat_id')->nullable();
            $table->text('action');
            $table->json('value')->nullable();
            $table->text('category')->nullable();
            $table->timestamp('collected_at')->useCurrent();

            $table->foreign('chat_id')->references('chat_id')->on('chats');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
