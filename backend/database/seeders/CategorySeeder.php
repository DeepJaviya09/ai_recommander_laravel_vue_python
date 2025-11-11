<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and gadgets',
            ],
            [
                'name' => 'Home & Kitchen',
                'slug' => 'home-kitchen',
                'description' => 'Home and kitchen products',
            ],
            [
                'name' => 'Books',
                'slug' => 'books',
                'description' => 'Books and reading materials',
            ],
            [
                'name' => 'Toys',
                'slug' => 'toys',
                'description' => 'Toys and games',
            ],
            [
                'name' => 'Clothing',
                'slug' => 'clothing',
                'description' => 'Clothing and apparel',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
