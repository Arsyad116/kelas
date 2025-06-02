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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('idorderdetail');
            $table->foreignId('idorder')->constrained(
                table: 'orders',
                column: 'idorder'
            )->onDelete('cascade');
            $table->string('menu', 100);
            $table->decimal('harga', 10, 2);
            $table->integer('jumlah');
            $table->decimal('subtotal', 10, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('idorder');
            $table->index('menu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
