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
        Schema::create('loads', function (Blueprint $table) {
            $table->id();
            $table->string('aol_number')->unique(); 
            $table->string('origin');
            $table->string('destination');
            $table->string('payer'); 
            $table->string('equipment_type')->nullable();
            $table->decimal('weight', 10, 2);
            $table->date('delivery_deadline');
            $table->string('customer_po')->nullable(); 
            $table->boolean('is_hazmat')->default(false); 
            $table->boolean('is_inbond')->default(false); 
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loads');
    }
};
