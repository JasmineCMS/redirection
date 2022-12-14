<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jasmine_redirections', function (Blueprint $table) {
            $table->id();
            $table->text('from');
            $table->text('to');
            $table->boolean('regex');
            $table->boolean('permanent');
            $table->boolean('enabled');
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
        Schema::dropIfExists('jasmine_redirections');
    }
};
