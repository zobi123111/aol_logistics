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
            $table->string('truck_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_contact_no')->nullable();
            $table->string('shipment_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->dropColumn(['truck_number', 'driver_name', 'driver_contact_no', 'shipment_status']);
        });
    }
};
