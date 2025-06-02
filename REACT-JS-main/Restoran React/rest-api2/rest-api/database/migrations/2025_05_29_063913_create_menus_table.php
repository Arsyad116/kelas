<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id('idmenu');
            $table->string('menu', 100);

            $table->unsignedBigInteger('idkategori');
            $table->foreign('idkategori')
                ->references('idkategori')
                ->on('kategoris')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->decimal('harga', 10, 2);
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['tersedia', 'habis'])->default('tersedia');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['idkategori']);
        });

        Schema::dropIfExists('menus');
    }
}
