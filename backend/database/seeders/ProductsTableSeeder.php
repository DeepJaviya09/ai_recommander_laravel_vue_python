<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');

        $products = [
            [
                'name' => 'Wireless Earbuds',
                'description' =>
                    'Premium Bluetooth 5.3 wireless earbuds engineered with advanced active noise cancellation, deep bass audio drivers, and an ergonomic in-ear silicone design that offers a secure and comfortable fit for long listening sessions. Includes a compact magnetic charging case providing multiple recharges, touch controls for music and calls, and sweat-resistant coating suitable for gym workouts, running, daily commuting, and hands-free voice assistants. Ideal for users who want immersive sound, portability, and seamless connectivity across devices.',
                'category' => 'Electronics',
                'tags' => ['bluetooth','audio','headphones'],
                'price' => 49.99,
                'image_url' => 'https://images.unsplash.com/photo-1517059224940-d4af9eec41e5',
            ],

            [
                'name' => 'Smart LED Lamp',
                'description' =>
                    'Wi-Fi enabled RGB smart LED lamp capable of displaying millions of color shades with dynamic scenes, brightness automation, and customizable schedules. Works smoothly with voice assistants like Alexa and Google Home. Perfect for ambient mood lighting, reading corners, bedside tables, gaming room setups, and modern smart-home environments. Features low-energy LED technology, smooth dimming, and app-based control for full personalization.',
                'category' => 'Home & Kitchen',
                'tags' => ['lamp','light','smart home'],
                'price' => 29.99,
                'image_url' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511',
            ],

            [
                'name' => 'Stainless Steel Water Bottle',
                'description' =>
                    'Durable 18/8 food-grade stainless steel insulated bottle designed to maintain optimal beverage temperature—keeping drinks cold for up to 24 hours and hot for up to 12 hours. Features a leak-proof twist cap, sweat-free matte finish, and wide mouth for easy cleaning and ice placement. Ideal for outdoor hiking, office use, gym workouts, travel backpacks, and everyday hydration. Built to withstand drops, temperature changes, and daily wear.',
                'category' => 'Home & Kitchen',
                'tags' => ['bottle','insulated','outdoors'],
                'price' => 19.99,
                'image_url' => 'https://picsum.photos/seed/bottle/600/400',
            ],

            [
                'name' => 'Noise Cancelling Headphones',
                'description' =>
                    'High-end over-ear headphones featuring hybrid active noise cancellation that filters out background chatter, engine noise, and environmental distractions. Equipped with large 40mm audio drivers delivering rich bass, balanced mids, and crisp highs. Offers up to 30 hours of playtime, soft memory-foam ear cushions, adjustable steel headband, and quick-charge support. Ideal for remote work, flights, gaming, music production, and immersive audio experiences.',
                'category' => 'Electronics',
                'tags' => ['audio','headphones','anc'],
                'price' => 129.99,
                'image_url' => 'https://picsum.photos/seed/headphones/600/400',
            ],

            [
                'name' => '4K Action Camera',
                'description' =>
                    'Compact 4K UHD action camera built for adventure sports and travel documentation. Offers advanced electronic image stabilization, waterproof housing up to 40m, slow-motion modes, time-lapse capture, and a wide-angle 170° lens. Suitable for biking, hiking, underwater diving, extreme sports, and vlogging. Includes mounting accessories for helmets, tripods, dashboards, and chest straps.',
                'category' => 'Electronics',
                'tags' => ['camera','sports','outdoor'],
                'price' => 199.00,
                'image_url' => 'https://picsum.photos/seed/camera/600/400',
            ],

            [
                'name' => 'Fantasy Novel: The Lost Realm',
                'description' =>
                    'An epic, world-building fantasy novel set in a vast mystical realm filled with ancient kingdoms, forgotten magic, mythical beasts, enchanted forests, and a prophecy-driven storyline. Follows a young hero’s journey through intense battles, hidden secrets, and alliances with otherworldly beings. Ideal for readers who enjoy immersive lore, character-driven storytelling, and richly detailed fantasy worlds.',
                'category' => 'Books',
                'tags' => ['fantasy','novel','book'],
                'price' => 12.50,
                'image_url' => 'https://picsum.photos/seed/book1/600/400',
            ],

            [
                'name' => 'Cookbook: Quick & Healthy',
                'description' =>
                    'A comprehensive cookbook featuring over 100 fast and nutritious recipes designed for individuals with busy schedules. Includes meal prep techniques, calorie-conscious meal plans, high-protein dishes, vegetarian options, and easy step-by-step instructions. Ideal for beginners and home cooks looking to adopt healthier eating habits without sacrificing flavor or time.',
                'category' => 'Books',
                'tags' => ['cookbook','health','recipes'],
                'price' => 18.00,
                'image_url' => 'https://picsum.photos/seed/book2/600/400',
            ],

            [
                'name' => 'STEM Building Blocks',
                'description' =>
                    'STEM-themed educational building block kit designed to improve creativity, early engineering skills, problem-solving, and cognitive development. Includes colorful interlocking pieces that encourage open-ended construction, logical reasoning, and hands-on experimentation. Perfect for classrooms, home learning environments, and gift purposes for curious young minds.',
                'category' => 'Toys',
                'tags' => ['stem','kids','blocks'],
                'price' => 24.99,
                'image_url' => 'https://picsum.photos/seed/toys1/600/400',
            ],

            [
                'name' => 'Plush Teddy Bear',
                'description' =>
                    'A soft, high-quality plush teddy bear made with hypoallergenic materials and premium stuffing. Designed to be comforting, huggable, and safe for children. Perfect as a birthday gift, nursery decoration, bedtime companion, or emotional support plush for toddlers and young kids.',
                'category' => 'Toys',
                'tags' => ['plush','kids','gift'],
                'price' => 14.99,
                'image_url' => 'https://picsum.photos/seed/toys2/600/400',
            ],

            [
                'name' => 'Men\'s Classic T-Shirt',
                'description' =>
                    'A breathable 100% cotton men’s T-shirt featuring a classic fit, reinforced stitching, and soft-touch fabric ideal for everyday casual wear. Works well as a standalone top or for layering with jackets and hoodies. Suitable for outdoor activities, lounging, travel, and daily comfort.',
                'category' => 'Clothing',
                'tags' => ['men','shirt','casual'],
                'price' => 15.99,
                'image_url' => 'https://picsum.photos/seed/shirt/600/400',
            ],

            [
                'name' => 'Women\'s Yoga Leggings',
                'description' =>
                    'Stretchable and moisture-wicking women’s yoga leggings designed for high-mobility activities such as yoga, dance, pilates, running, and gym workouts. Features a body-hugging fit, supportive waistband, and breathable fabric offering comfort during long activity sessions or everyday athleisure wear.',
                'category' => 'Clothing',
                'tags' => ['women','leggings','fitness'],
                'price' => 22.99,
                'image_url' => 'https://picsum.photos/seed/leggings/600/400',
            ],

            [
                'name' => 'Espresso Coffee Maker',
                'description' =>
                    'Compact home espresso coffee maker engineered to brew rich, barista-style espresso with a deep crema layer. Features fast heating, easy-to-clean components, a reusable steel filter, and a sleek compact form factor suitable for kitchens, office desks, and small apartments.',
                'category' => 'Home & Kitchen',
                'tags' => ['coffee','kitchen','appliance'],
                'price' => 89.99,
                'image_url' => 'https://picsum.photos/seed/coffee/600/400',
            ],

            [
                'name' => 'Ergonomic Office Chair',
                'description' =>
                    'Ergonomic office chair featuring adjustable lumbar support, breathable mesh backrest, cushioned seat padding, and smooth-rolling wheels for mobility. Designed to promote healthy posture, reduce back strain, and support long working hours at a desk. Ideal for home offices, corporate workspaces, and study rooms.',
                'category' => 'Home & Kitchen',
                'tags' => ['office','chair','ergonomic'],
                'price' => 149.99,
                'image_url' => 'https://picsum.photos/seed/chair/600/400',
            ],

            [
                'name' => 'Wireless Charging Pad',
                'description' =>
                    'Qi-certified fast wireless charging pad compatible with major smartphones and earbuds. Features overheating protection, non-slip surface, LED charging indicators, and a slim, compact profile ideal for nightstands, work desks, and travel use.',
                'category' => 'Electronics',
                'tags' => ['charger','wireless','phone'],
                'price' => 25.00,
                'image_url' => 'https://picsum.photos/seed/charger/600/400',
            ],

            [
                'name' => 'Mystery Thriller: Hidden Truths',
                'description' =>
                    'A gripping psychological thriller filled with suspense, investigative drama, dark secrets, and unexpected plot twists. Follows a detective unraveling a complex web of lies while confronting hidden truths buried beneath layers of deception. Ideal for fans of crime fiction, mystery novels, and fast-paced thrillers.',
                'category' => 'Books',
                'tags' => ['thriller','mystery','book'],
                'price' => 14.00,
                'image_url' => 'https://picsum.photos/seed/book3/600/400',
            ],
        ];

        foreach ($products as $data) {
            $category = $data['category'];
            $categoryId = $categories[$category]->id ?? null;

            if (!$categoryId) continue;

            unset($data['category']);
            $data['category_id'] = $categoryId;

            if (is_array($data['tags'])) {
                $data['tags'] = array_values($data['tags']);
            }

            Product::create($data);
        }
    }
}
