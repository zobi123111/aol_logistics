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
        Schema::table('services', function (Blueprint $table) {
            $table->string('origin')->nullable()->change();
            $table->string('destination')->nullable()->change();
            $table->string('street')->nullable()->after('destination');
            $table->string('city')->nullable()->after('street');
            $table->string('state')->nullable()->after('city');
            $table->string('zip')->nullable()->after('state');
            $table->string('country')->nullable()->after('zip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('origin')->nullable(false)->change();
            $table->string('destination')->nullable(false)->change();
            $table->dropColumn(['street', 'city', 'state', 'zip', 'country']);

        });
    }
};
