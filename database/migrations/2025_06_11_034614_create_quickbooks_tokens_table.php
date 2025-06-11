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
        Schema::create('quickbooks_tokens', function (Blueprint $table) {
            $table->id();
            $table->longText('access_token');
            $table->longText('refresh_token');
            $table->timestamp('access_token_expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quickbooks_tokens');
    }
};
