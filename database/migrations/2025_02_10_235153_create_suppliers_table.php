<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('dba')->nullable();
            $table->string('street_address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('country');
            $table->string('office_phone')->nullable();
            $table->string('primary_contact_email')->nullable();
            $table->string('primary_contact_office_phone')->nullable();
            $table->string('primary_contact_mobile_phone')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_office_phone')->nullable();
            $table->string('user_mobile_phone')->nullable();
            $table->string('user_role')->nullable();
            $table->string('service_type')->nullable();
            $table->string('currency');
            $table->string('preferred_language');
            $table->json('documents')->nullable();
            $table->string('scac_number')->nullable();
            $table->json('scac_documents')->nullable();
            $table->string('caat_number')->nullable();
            $table->json('caat_documents')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add soft delete column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
