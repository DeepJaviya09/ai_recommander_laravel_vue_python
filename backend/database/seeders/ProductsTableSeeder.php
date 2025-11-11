<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories by name for mapping
        $categories = Category::all()->keyBy('name');

        $products = [
            [
                'name' => 'Wireless Earbuds',
                'description' => 'Bluetooth 5.3 earbuds with noise cancellation.',
                'category' => 'Electronics',
                'tags' => ['bluetooth','audio','headphones'],
                'price' => 49.99,
                'image_url' => 'https://images.unsplash.com/photo-1517059224940-d4af9eec41e5',
            ],
            [
                'name' => 'Smart LED Lamp',
                'description' => 'Wi-Fi controlled color-changing lamp.',
                'category' => 'Home & Kitchen',
                'tags' => ['lamp','light','smart home'],
                'price' => 29.99,
                'image_url' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511',
            ],
            [
                'name' => 'Stainless Steel Water Bottle',
                'description' => 'Insulated bottle keeps drinks cold for 24 hours.',
                'category' => 'Home & Kitchen',
                'tags' => ['bottle','insulated','outdoors'],
                'price' => 19.99,
                'image_url' => 'https://picsum.photos/seed/bottle/600/400',
            ],
            [
                'name' => 'Noise Cancelling Headphones',
                'description' => 'Over-ear, active noise cancellation, 30h battery.',
                'category' => 'Electronics',
                'tags' => ['audio','headphones','anc'],
                'price' => 129.99,
                'image_url' => 'https://picsum.photos/seed/headphones/600/400',
            ],
            [
                'name' => '4K Action Camera',
                'description' => 'Waterproof action camera with stabilization.',
                'category' => 'Electronics',
                'tags' => ['camera','sports','outdoor'],
                'price' => 199.00,
                'image_url' => 'https://picsum.photos/seed/camera/600/400',
            ],
            [
                'name' => 'Fantasy Novel: The Lost Realm',
                'description' => 'Epic fantasy adventure in a mystical world.',
                'category' => 'Books',
                'tags' => ['fantasy','novel','book'],
                'price' => 12.50,
                'image_url' => 'https://picsum.photos/seed/book1/600/400',
            ],
            [
                'name' => 'Cookbook: Quick & Healthy',
                'description' => 'Easy recipes for busy weekdays.',
                'category' => 'Books',
                'tags' => ['cookbook','health','recipes'],
                'price' => 18.00,
                'image_url' => 'https://picsum.photos/seed/book2/600/400',
            ],
            [
                'name' => 'STEM Building Blocks',
                'description' => 'Educational blocks that encourage creativity.',
                'category' => 'Toys',
                'tags' => ['stem','kids','blocks'],
                'price' => 24.99,
                'image_url' => 'https://picsum.photos/seed/toys1/600/400',
            ],
            [
                'name' => 'Plush Teddy Bear',
                'description' => 'Soft and cuddly companion for kids.',
                'category' => 'Toys',
                'tags' => ['plush','kids','gift'],
                'price' => 14.99,
                'image_url' => 'https://picsum.photos/seed/toys2/600/400',
            ],
            [
                'name' => 'Men\'s Classic T-Shirt',
                'description' => '100% cotton, breathable everyday wear.',
                'category' => 'Clothing',
                'tags' => ['men','shirt','casual'],
                'price' => 15.99,
                'image_url' => 'https://picsum.photos/seed/shirt/600/400',
            ],
            [
                'name' => 'Women\'s Yoga Leggings',
                'description' => 'Stretchy, comfortable leggings for workouts.',
                'category' => 'Clothing',
                'tags' => ['women','leggings','fitness'],
                'price' => 22.99,
                'image_url' => 'https://picsum.photos/seed/leggings/600/400',
            ],
            [
                'name' => 'Espresso Coffee Maker',
                'description' => 'Compact machine for rich espresso at home.',
                'category' => 'Home & Kitchen',
                'tags' => ['coffee','kitchen','appliance'],
                'price' => 89.99,
                'image_url' => 'https://picsum.photos/seed/coffee/600/400',
            ],
            [
                'name' => 'Ergonomic Office Chair',
                'description' => 'Lumbar support and breathable mesh back.',
                'category' => 'Home & Kitchen',
                'tags' => ['office','chair','ergonomic'],
                'price' => 149.99,
                'image_url' => 'https://picsum.photos/seed/chair/600/400',
            ],
            [
                'name' => 'Wireless Charging Pad',
                'description' => 'Fast Qi charging for smartphones.',
                'category' => 'Electronics',
                'tags' => ['charger','wireless','phone'],
                'price' => 25.00,
                'image_url' => 'https://picsum.photos/seed/charger/600/400',
            ],
            [
                'name' => 'Mystery Thriller: Hidden Truths',
                'description' => 'Page-turner thriller with unexpected twists.',
                'category' => 'Books',
                'tags' => ['thriller','mystery','book'],
                'price' => 14.00,
                'image_url' => 'https://picsum.photos/seed/book3/600/400',
            ],
        ];

        foreach ($products as $index => $data) {
            // Get category_id from category name
            $categoryName = $data['category'];
            $categoryId = $categories[$categoryName]->id ?? null;
            
            if (!$categoryId) {
                continue; // Skip if category not found
            }

            // Remove category and add category_id
            unset($data['category']);
            $data['category_id'] = $categoryId;

            // Allow tags as JSON array or string per spec
            if (is_array($data['tags'])) {
                $data['tags'] = array_values($data['tags']);
            }
            Product::create($data);
        }
    }
}
