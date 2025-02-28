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
        Schema::table('assigned_services', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('cancel_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assigned_services', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('cancel_reason');
        });
    }
};
