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
            $table->unsignedBigInteger('supplier_invoice_id')->nullable()->after('id');

            // Foreign Key Constraint
            $table->foreign('supplier_invoice_id')->references('id')->on('supplier_invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->dropForeign(['supplier_invoice_id']);
            $table->dropColumn('supplier_invoice_id');
        });
    }
};
