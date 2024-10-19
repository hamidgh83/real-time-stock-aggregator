<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 10);
            $table->dateTime('timestamp');
            $table->decimal('open', 10, 4);
            $table->decimal('high', 10, 4);
            $table->decimal('low', 10, 4);
            $table->decimal('close', 10, 4);
            $table->unsignedBigInteger('volume');

            // Unique constraint to prevent duplicate entries for the same symbol and timestamp
            $table->unique(['symbol', 'timestamp']);

            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_prices');
    }
};
