<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizational_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('includeDefaultMaterials');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizational_settings');
    }
}
