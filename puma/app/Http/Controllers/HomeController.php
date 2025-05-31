<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        try {
            // Ambil data banner dari database
            $banners = Banner::orderBy('id', 'asc')
                ->pluck('image_path')
                ->toArray();
        } catch (\Exception $e) {
            $banners = [];
        }

        try {
            // Ambil data produk dari database (jika ada)
            $products = Product::orderBy('id', 'asc')
                ->get();

            // Jika tidak ada produk di database, buat data dummy
            if ($products->isEmpty()) {
                $products = [
                    (object) [
                        'id' => 1,
                        'name' => 'PUMA Palermo I',
                        'price' => 876000,
                        'image' => 'black.jpeg'
                    ],
                    (object) [
                        'id' => 2,
                        'name' => 'PUMA Palermo II',
                        'price' => 680000,
                        'image' => 'brown.jpeg'
                    ],
                    (object) [
                        'id' => 3,
                        'name' => 'PUMA Palermo III',
                        'price' => 750000,
                        'image' => 'green.jpeg'
                    ]
                ];
            }
        } catch (\Exception $e) {
            // Buat data dummy jika terjadi error
            $products = [
                (object) [
                    'id' => 1,
                    'name' => 'PUMA Palermo I',
                    'price' => 876000,
                    'image' => 'black.jpeg'
                ],
                (object) [
                    'id' => 2,
                    'name' => 'PUMA Palermo II',
                    'price' => 680000,
                    'image' => 'brown.jpeg'
                ],
                (object) [
                    'id' => 3,
                    'name' => 'PUMA Palermo III',
                    'price' => 750000,
                    'image' => 'green.jpeg'
                ]
            ];
        }

        return view('index', [
            'banners' => $banners,
            'products' => $products
        ]);
    }
}
