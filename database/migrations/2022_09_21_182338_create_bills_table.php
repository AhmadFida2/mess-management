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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->date('month');
            $table->integer('units');
            $table->float('amount');
            $table->tinyInteger('status')->default(0); //0-Unpaid,1-Partial,2-Paid
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();;
            $table->string('bill_details')->virtualAs('concat(month," (",amount,")")');
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
        Schema::dropIfExists('bills');
    }
};
