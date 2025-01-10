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
        Schema::create('contact_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignUuid('contact_id');
            $table->schemalessAttributes('meta');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_user');
    }
};
