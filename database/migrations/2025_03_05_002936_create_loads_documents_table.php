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
        Schema::create('loads_documents', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('load_id')->constrained('loads')->onDelete('cascade'); 
            $table->string('document_type'); 
            $table->string('path'); 
            $table->timestamps(); 
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loads_documents');
    }
};
