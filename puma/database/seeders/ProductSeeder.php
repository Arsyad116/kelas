<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data produk yang ada (opsional)
        DB::table('produk')->truncate();

        // Daftar produk yang akan ditambahkan
        $products = [
            [
                'nama' => 'PUMA Palermo I',
                'harga' => 876000,
                'gambar' => 'black.jpeg',
                'stok' => 10,
                'kategori' => 'Sepatu',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'PUMA Palermo II',
                'harga' => 680000,
                'gambar' => 'brown.jpeg',
                'stok' => 15,
                'kategori' => 'Sepatu',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'PUMA Palermo III',
                'harga' => 750000,
                'gambar' => 'green.jpeg',
                'stok' => 8,
                'kategori' => 'Sepatu',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'PUMA Suede Classic',
                'harga' => 950000,
                'gambar' => 'suede.jpg',
                'stok' => 12,
                'kategori' => 'Sepatu',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'PUMA RS-X',
                'harga' => 1200000,
                'gambar' => 'rsx.jpg',
                'stok' => 7,
                'kategori' => 'Sepatu',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'PUMA T-Shirt Basic',
                'harga' => 350000,
                'gambar' => 'tshirt.jpg',
                'stok' => 20,
                'kategori' => 'Pakaian',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Masukkan data produk ke database
        DB::table('produk')->insert($products);

        $this->command->info('Produk berhasil ditambahkan ke database!');
    }
}
