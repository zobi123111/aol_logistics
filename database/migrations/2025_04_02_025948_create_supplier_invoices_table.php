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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('load_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('file_path'); // Path to the uploaded file
            $table->string('quickbook_invoice_id')->nullable(); // Nullable QuickBooks ID
            $table->string('status')->default('pending'); // Fix status field
            $table->timestamps();

            // Foreign Keys
            $table->foreign('load_id')->references('id')->on('loads')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
