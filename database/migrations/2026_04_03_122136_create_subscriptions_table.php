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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // кто подписался
            $table->foreignId('author_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            // на кого подписались
            $table->foreignId('target_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['author_id', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
