<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('date');
            $table->unsignedBigInteger('amount');
            $table->unsignedFloat('exchange_rate');
            $table->unsignedBigInteger('source_transaction_id');
            $table->unsignedBigInteger('destination_transaction_id');
        });
    }
};
