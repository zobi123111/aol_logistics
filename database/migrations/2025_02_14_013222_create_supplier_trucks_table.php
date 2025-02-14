<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('supplier_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id'); 
            $table->string('unit_type'); 
            $table->string('unit_number'); 
            $table->string('license_plate'); 
            $table->string('state'); 
            $table->softDeletes(); 
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_units');
    }
};

