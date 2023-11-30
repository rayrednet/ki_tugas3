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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 64);
            $table->string('password', 64);
            $table->string('key_public', 4096);
            $table->text('key_private', 4096);
            $table->text('key_enkripsi', 4096);
            $table->text('digital_signature_public')->nullable();
            $table->text('digital_signature_private')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
