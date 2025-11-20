<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->enum("request_from", ["vendor", "admin"]);

            $table->unsignedBigInteger('sender_id')->nullable();
            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('reciever_id');
            $table->foreign('reciever_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->text("before_sending_money");

            $table->text("sent_amount");

            $table->text("after_sending_amount");

            $table->text("invoice")->nullable();

            $table->enum("status", ["new","accepted","rejected"]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
