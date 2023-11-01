<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_prints', function (Blueprint $table) {
            $table->id();

            $table->string('filename_1st', 100);
            $table->foreign('first_id')->references('id')->on('photos')->onDelete("cascade");
            $table->string('filename_2nd', 100)->nullable();
            $table->foreign('second_id')->references('id')->on('photos')->onDelete("set null");
            $table->dateTime('printed_at')->nullable()->default(null)->nullable();

            $table->string('filename_merged', 100)->nullable();

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
        Schema::dropIfExists('pending_prints');
    }
};
