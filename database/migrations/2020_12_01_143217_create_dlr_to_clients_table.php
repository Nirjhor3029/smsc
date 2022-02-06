<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDlrToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dlr_to_clients', function (Blueprint $table) {
            $table->id();
            $table->integer('is_dlr_sent')->default(0);
            $table->string('t_msg_id')->nullable()->index();
            $table->string('dlr_status')->nullable();
            $table->string('client_response')->nullable();
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
        Schema::dropIfExists('dlr_to_clients');
    }
}
