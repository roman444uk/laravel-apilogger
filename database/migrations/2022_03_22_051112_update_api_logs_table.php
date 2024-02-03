<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('api_logs');

        Schema::create('api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('method');
            $table->string('url');
            $table->longText('payload');
            $table->longText('payload_raw')->nullable();
            $table->longText('response');
            $table->text('headers');
            $table->longText('response_headers');
            $table->integer('status_code');
            $table->string('duration');
            $table->string('controller');
            $table->string('action');
            $table->string('models');
            $table->string('ip');
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
        Schema::dropIfExists('api_logs');
    }
}
