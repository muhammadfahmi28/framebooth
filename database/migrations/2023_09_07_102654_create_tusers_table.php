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
        Schema::create('tusers', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 100)->unique();       // id for frontend, e.g. url name
            $table->string('name', 100)->default("");   // cosmetic name
            $table->string('code', 100);                // qr_code
            // $table->boolean('is_used')->default(false); // used
            $table->integer('max_photos')->unsigned();

            $table->timestamp('autodelete_on')->nullable(); // autodelete for disposable account, can be set after first scanned

            $table->date('valid_until')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // INTEGRATION WITH MEMBER, FUTURE FEATURE
            // $table->unsignedBigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
