<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $products = [
            ['name' => 'Stainless Steel Water Bottle 750ml', 'sku' => 'SKU-WSB-001', 'price' => 19.99, 'stock' => 120, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Wireless Optical Mouse',              'sku' => 'SKU-MOU-002', 'price' => 24.90, 'stock' => 150, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mechanical Keyboard (87-Key)',        'sku' => 'SKU-KBD-003', 'price' => 79.00, 'stock' => 80,  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'USB-C Cable 1m (Fast Charge)',        'sku' => 'SKU-CAB-004', 'price' => 8.50,  'stock' => 300, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bluetooth Earbuds w/ Case',           'sku' => 'SKU-EBR-005', 'price' => 39.95, 'stock' => 110, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'A5 Notebook (200 Pages)',             'sku' => 'SKU-NTB-006', 'price' => 4.99,  'stock' => 500, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'LED Desk Lamp (Adjustable)',          'sku' => 'SKU-LMP-007', 'price' => 29.90, 'stock' => 70,  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ceramic Coffee Mug 350ml',            'sku' => 'SKU-MUG-008', 'price' => 9.90,  'stock' => 200, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Daypack Backpack 20L',                'sku' => 'SKU-BPK-009', 'price' => 49.00, 'stock' => 60,  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Power Bank 10,000 mAh',               'sku' => 'SKU-PBK-010', 'price' => 25.00, 'stock' => 140, 'created_at' => $now, 'updated_at' => $now],
        ];

        Product::insert($products);
    }
}
