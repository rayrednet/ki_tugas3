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
        Schema::create('key_sharing', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id');
            $table->foreignUuid('user_id_tujuan');
            $table->string('key')->nullable(); 
            $table->enum('status', ['allowed', 'pending', 'rejected'])->default('pending');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('user_id_tujuan')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_sharing');
    }
};
