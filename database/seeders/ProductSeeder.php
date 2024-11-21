<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Storage::makeDirectory('public/products');

        $products = [
            [
                'name' => 'Minyak Bimoli',
                'price' => 100000,
                'discount' => 1000,
                'stock' => 100,
                'description' => 'Minyak Bimoli',
                'image_path' => 'seed/bimoli.jpg'
            ],
            [
                'name' => 'Gulaku',
                'price' => 200000,
                'discount' => 15,
                'stock' => 50,
                'description' => 'Gulaku',
                'image_path' => 'seed/gulaku.jpg'
            ],
            [
                'name' => 'Sabun Batang Lux',
                'price' => 3000,
                'discount' => 0,
                'stock' => 100,
                'description' => 'Sabun Batang Lux',
                'image_path' => 'seed/lux.jpg'
            ],
            [
                'name' => 'Sampo Pantene',
                'price' => 10000,
                'discount' => 0,
                'stock' => 100,
                'description' => 'Sampo Pantene',
                'image_path' => 'seed/pantene.jpg'
            ],
            [
                'name' => 'Telur',
                'price' => 15000,
                'discount' => 0,
                'stock' => 100,
                'description' => 'Telur',
                'image_path' => 'seed/telur.jpg'
            ],
            [
                'name' => 'Tepung Terigu Segitiga Biru',
                'price' => 35000,
                'discount' => 1000,
                'stock' => 100,
                'description' => 'Tepung Terigu Segitiga Biru',
                'image_path' => 'seed/tepung-segitiga-biru.jpg'
            ],
        ];

        foreach ($products as $product) {
            // Get the source path of the seed image
            $sourcePath = storage_path('app/public/products/' . $product['image_path']);

            // Generate a unique filename for the new image
            $filename = uniqid() . '.jpg';

            // Copy the seed image to the products directory
            if (File::exists($sourcePath)) {
                File::copy(
                    $sourcePath,
                    storage_path('app/public/products/' . $filename)
                );
            }

            // Create the product with the new image filename
            Product::create([
                'name' => $product['name'],
                'price' => $product['price'],
                'discount' => $product['discount'],
                'stock' => $product['stock'],
                'description' => $product['description'],
                'image' => $filename
            ]);
        }
    }
}
