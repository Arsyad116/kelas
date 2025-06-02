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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('idorder');
            $table->foreignId('idpelanggan')->constrained(
                table: 'pelanggans',
                column: 'idpelanggan'
            )->onDelete('cascade');
            $table->decimal('total', 12, 2);
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('idpelanggan');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
