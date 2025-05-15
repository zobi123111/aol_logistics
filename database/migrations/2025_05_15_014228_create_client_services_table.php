<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientServicesTable extends Migration
{
    public function up()
    {
        Schema::create('client_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('master_service_id')->constrained('master_services')->onDelete('cascade');
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->date('service_date')->nullable();
            $table->decimal('schedule_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'master_service_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_services');
    }
}
