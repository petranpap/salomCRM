<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class DemoProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'sku' => 'P001',
                'name' => 'Organic Shampoo',
                'category' => 'Hair Care',
                'brand' => 'Nature\'s Best',
                'cost_price' => 10.00,
                'sell_price' => 15.00,
                'stock_qty' => 50,
                'threshold_qty' => 10,
                'status' => 'active',
            ],
            [
                'sku' => 'P002',
                'name' => 'Moisturizing Conditioner',
                'category' => 'Hair Care',
                'brand' => 'Nature\'s Best',
                'cost_price' => 12.00,
                'sell_price' => 18.00,
                'stock_qty' => 30,
                'threshold_qty' => 5,
                'status' => 'active',
            ],
            [
                'sku' => 'P003',
                'name' => 'Nail Polish',
                'category' => 'Nail Care',
                'brand' => 'Colorful Nails',
                'cost_price' => 5.00,
                'sell_price' => 8.00,
                'stock_qty' => 100,
                'threshold_qty' => 20,
                'status' => 'active',
            ],
            [
                'sku' => 'P004',
                'name' => 'Facial Cleanser',
                'category' => 'Skin Care',
                'brand' => 'Glow Skin',
                'cost_price' => 15.00,
                'sell_price' => 22.00,
                'stock_qty' => 20,
                'threshold_qty' => 5,
                'status' => 'active',
            ],
            [
                'sku' => 'P005',
                'name' => 'Hair Styling Gel',
                'category' => 'Hair Care',
                'brand' => 'Style It',
                'cost_price' => 8.00,
                'sell_price' => 12.00,
                'stock_qty' => 40,
                'threshold_qty' => 10,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}