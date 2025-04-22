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
        Schema::table('loads', function (Blueprint $table) {
            $table->decimal('reefer_temperature', 5, 2)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->dropColumn('reefer_temperature');

        });
    }
};
