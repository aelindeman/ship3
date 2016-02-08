<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UPSMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamp('time')->default(app('db')->raw('CURRENT_TIMESTAMP'));

            $table->string('status')->nullable();
            $table->float('bcharge')->nullable();
            $table->float('loadpct')->nullable();
            $table->float('battv')->nullable();
            $table->float('linev')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ups');
    }
}
