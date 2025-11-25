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

            // Additional Books for better recommendations
            [
                'name' => 'Science Fiction: Beyond the Stars',
                'description' =>
                    'An epic space opera set in a distant galaxy where humanity has colonized multiple planets. Features interstellar travel, alien civilizations, political intrigue, and advanced technology. Follows a crew of explorers as they uncover ancient secrets that could change the fate of the universe. Perfect for fans of space exploration, futuristic technology, and epic storytelling.',
                'category' => 'Books',
                'tags' => ['sci-fi','space','fiction','novel'],
                'price' => 16.99,
                'image_url' => 'https://picsum.photos/seed/book4/600/400',
            ],

            [
                'name' => 'Historical Fiction: The Crown\'s Secret',
                'description' =>
                    'A captivating historical fiction novel set in medieval Europe during the reign of a powerful kingdom. Blends real historical events with fictional characters, court intrigue, battles, and romance. Explores themes of power, loyalty, and sacrifice in a richly detailed historical setting. Ideal for readers who enjoy period dramas and historical narratives.',
                'category' => 'Books',
                'tags' => ['historical','fiction','medieval','drama'],
                'price' => 15.50,
                'image_url' => 'https://picsum.photos/seed/book5/600/400',
            ],

            [
                'name' => 'Self-Help: The Productivity Master',
                'description' =>
                    'A comprehensive guide to maximizing productivity and achieving your goals. Covers time management techniques, habit formation, focus strategies, and work-life balance. Includes practical exercises, real-world examples, and actionable advice for professionals, students, and entrepreneurs. Perfect for anyone looking to improve their efficiency and achieve more in less time.',
                'category' => 'Books',
                'tags' => ['self-help','productivity','business','motivation'],
                'price' => 19.99,
                'image_url' => 'https://picsum.photos/seed/book6/600/400',
            ],

            [
                'name' => 'Romance Novel: Love in Paris',
                'description' =>
                    'A heartwarming contemporary romance set in the beautiful city of Paris. Follows two strangers who meet by chance and discover that love can be found in the most unexpected places. Features charming characters, romantic settings, and a story that will make you believe in true love. Perfect for fans of romantic fiction and feel-good stories.',
                'category' => 'Books',
                'tags' => ['romance','fiction','love','contemporary'],
                'price' => 13.99,
                'image_url' => 'https://picsum.photos/seed/book7/600/400',
            ],

            [
                'name' => 'Biography: The Innovator\'s Journey',
                'description' =>
                    'An inspiring biography of a tech entrepreneur who revolutionized the industry. Chronicles their journey from humble beginnings to building a billion-dollar company. Includes insights into their decision-making process, challenges faced, and lessons learned. A must-read for aspiring entrepreneurs and anyone interested in business success stories.',
                'category' => 'Books',
                'tags' => ['biography','business','entrepreneur','inspiration'],
                'price' => 22.00,
                'image_url' => 'https://picsum.photos/seed/book8/600/400',
            ],

            [
                'name' => 'Horror Novel: The Haunted Mansion',
                'description' =>
                    'A spine-chilling horror novel about a family who moves into an old mansion with a dark past. As strange events unfold, they discover the house holds terrifying secrets. Features atmospheric tension, supernatural elements, and psychological horror. Perfect for fans of horror fiction and ghost stories who enjoy being kept on the edge of their seat.',
                'category' => 'Books',
                'tags' => ['horror','thriller','supernatural','mystery'],
                'price' => 14.50,
                'image_url' => 'https://picsum.photos/seed/book9/600/400',
            ],

            [
                'name' => 'Adventure: The Lost Expedition',
                'description' =>
                    'An action-packed adventure novel following a team of explorers searching for a lost ancient city in the Amazon rainforest. Features jungle survival, ancient mysteries, dangerous wildlife, and unexpected discoveries. Combines adventure, archaeology, and suspense in a thrilling narrative. Ideal for readers who enjoy Indiana Jones-style adventures and exploration stories.',
                'category' => 'Books',
                'tags' => ['adventure','exploration','action','archaeology'],
                'price' => 17.99,
                'image_url' => 'https://picsum.photos/seed/book10/600/400',
            ],

            // Additional Electronics Products
            [
                'name' => 'Smart Fitness Watch Pro',
                'description' =>
                    'Advanced fitness tracking smartwatch featuring comprehensive health monitoring capabilities including heart rate tracking, sleep analysis, stress monitoring, and blood oxygen levels. Equipped with GPS for accurate distance and pace tracking during runs, cycling, and outdoor activities. Offers over 100 workout modes, water resistance up to 50 meters, and a vibrant AMOLED display with customizable watch faces. Includes smartphone notifications, music control, contactless payments, and a battery life of up to 7 days. Perfect for fitness enthusiasts, athletes, and health-conscious individuals who want detailed insights into their physical activity and wellness metrics.',
                'category' => 'Electronics',
                'tags' => ['watch','fitness','smartwatch','health'],
                'price' => 249.99,
                'image_url' => 'https://picsum.photos/seed/watch/600/400',
            ],

            [
                'name' => 'Portable Bluetooth Speaker',
                'description' =>
                    'Premium portable Bluetooth speaker delivering powerful 360-degree sound with deep bass and crystal-clear highs. Features advanced audio technology with dual passive radiators for enhanced low-frequency response. Equipped with IPX7 waterproof rating, making it perfect for pool parties, beach trips, and outdoor adventures. Offers up to 20 hours of continuous playback, built-in microphone for hands-free calls, and the ability to pair multiple speakers for stereo sound. Compact yet robust design with rugged construction that can withstand drops and impacts. Ideal for music lovers who want high-quality audio on the go.',
                'category' => 'Electronics',
                'tags' => ['speaker','bluetooth','audio','portable'],
                'price' => 79.99,
                'image_url' => 'https://picsum.photos/seed/speaker/600/400',
            ],

            [
                'name' => '4K Ultra HD Smart TV',
                'description' =>
                    'Stunning 55-inch 4K Ultra HD Smart TV featuring cutting-edge display technology with HDR10+ support for vibrant colors and exceptional contrast. Powered by a quad-core processor for smooth performance and fast app loading. Includes built-in streaming apps like Netflix, Amazon Prime Video, Disney+, and YouTube. Features voice control compatibility with Google Assistant and Alexa, multiple HDMI and USB ports, and advanced upscaling technology that enhances lower resolution content. Equipped with Dolby Vision and Dolby Atmos for immersive cinematic audio-visual experience. Perfect for home entertainment, gaming, and streaming your favorite content in stunning detail.',
                'category' => 'Electronics',
                'tags' => ['tv','smart tv','4k','entertainment'],
                'price' => 599.99,
                'image_url' => 'https://picsum.photos/seed/tv/600/400',
            ],

            [
                'name' => 'Mechanical Gaming Keyboard',
                'description' =>
                    'Professional mechanical gaming keyboard featuring premium Cherry MX switches for tactile feedback and precise keystrokes. Designed with RGB backlighting with millions of customizable colors and lighting effects that can be synchronized with games. Includes dedicated media controls, programmable macro keys, and a detachable wrist rest for comfort during long gaming sessions. Features N-key rollover technology for accurate simultaneous key presses, durable aluminum frame construction, and customizable keycaps. Compatible with Windows, Mac, and Linux. Perfect for gamers, programmers, and professionals who demand precision and durability from their keyboard.',
                'category' => 'Electronics',
                'tags' => ['keyboard','gaming','mechanical','rgb'],
                'price' => 129.99,
                'image_url' => 'https://picsum.photos/seed/keyboard/600/400',
            ],

            [
                'name' => 'Wireless Gaming Mouse',
                'description' =>
                    'High-performance wireless gaming mouse featuring advanced optical sensor with up to 16,000 DPI for pixel-perfect accuracy. Equipped with ultra-low latency wireless technology for lag-free gaming experience comparable to wired mice. Features ergonomic design with customizable weight system, programmable buttons, and RGB lighting zones. Offers up to 70 hours of battery life, fast charging capability, and onboard memory to save your settings. Includes precision tracking on various surfaces, adjustable polling rate, and software for customization. Ideal for competitive gamers and professionals who need precision and reliability.',
                'category' => 'Electronics',
                'tags' => ['mouse','gaming','wireless','precision'],
                'price' => 89.99,
                'image_url' => 'https://picsum.photos/seed/mouse/600/400',
            ],

            // Additional Home & Kitchen Products
            [
                'name' => 'Air Fryer Deluxe',
                'description' =>
                    'Large capacity air fryer with advanced rapid air circulation technology that cooks food up to 30% faster than traditional ovens while using 85% less oil. Features a spacious 6-quart basket that can accommodate meals for families, with adjustable temperature control from 180°F to 400°F and a 60-minute timer with auto-shutoff. Includes multiple cooking presets for fries, chicken, fish, steak, and vegetables, plus a digital touchscreen display for easy operation. Equipped with dishwasher-safe components, non-stick coating for easy cleaning, and safety features including cool-touch handles and automatic shutoff. Perfect for health-conscious individuals who want crispy, delicious food without the guilt of deep frying.',
                'category' => 'Home & Kitchen',
                'tags' => ['air fryer','kitchen','cooking','healthy'],
                'price' => 129.99,
                'image_url' => 'https://picsum.photos/seed/airfryer/600/400',
            ],

            [
                'name' => 'Stand Mixer Professional',
                'description' =>
                    'Heavy-duty stand mixer with powerful 1000-watt motor capable of handling the toughest mixing tasks from light whipping to heavy bread dough. Features a large 5.5-quart stainless steel bowl with a pouring shield to prevent splashing. Includes multiple attachments: flat beater for mixing, wire whip for whipping cream and eggs, and dough hook for kneading bread. Equipped with 10-speed settings, planetary mixing action that ensures thorough mixing, and a tilt-back head for easy access to the bowl. Built with durable construction and a splash guard. Perfect for serious bakers, home chefs, and anyone who loves to create delicious baked goods from scratch.',
                'category' => 'Home & Kitchen',
                'tags' => ['mixer','kitchen','baking','appliance'],
                'price' => 349.99,
                'image_url' => 'https://picsum.photos/seed/mixer/600/400',
            ],

            [
                'name' => 'Memory Foam Mattress Topper',
                'description' =>
                    'Premium gel-infused memory foam mattress topper designed to transform any mattress into a luxurious sleep surface. Features advanced cooling gel technology that regulates temperature and prevents overheating during sleep. The memory foam conforms to your body shape, providing personalized support and pressure relief for joints and muscles. Includes a removable, machine-washable cover made from breathable bamboo fabric. Available in multiple thickness options to suit different comfort preferences. Helps reduce motion transfer for couples and extends the life of your existing mattress. Perfect for anyone seeking improved sleep quality, back pain relief, or enhanced mattress comfort.',
                'category' => 'Home & Kitchen',
                'tags' => ['mattress','bedding','comfort','sleep'],
                'price' => 89.99,
                'image_url' => 'https://picsum.photos/seed/mattress/600/400',
            ],

            [
                'name' => 'Robot Vacuum Cleaner',
                'description' =>
                    'Intelligent robot vacuum cleaner with advanced navigation technology featuring laser mapping and room recognition for efficient cleaning. Equipped with powerful suction that automatically increases on carpets and rugs, and a large dustbin capacity. Features multiple cleaning modes including spot cleaning, edge cleaning, and scheduled cleaning via smartphone app. Includes virtual boundaries to prevent access to specific areas, automatic return to charging dock when battery is low, and compatibility with voice assistants. Equipped with sensors to avoid obstacles, stairs, and furniture. Perfect for busy households who want to maintain clean floors with minimal effort.',
                'category' => 'Home & Kitchen',
                'tags' => ['vacuum','robot','cleaning','smart'],
                'price' => 299.99,
                'image_url' => 'https://picsum.photos/seed/robot/600/400',
            ],

            [
                'name' => 'Stainless Steel Cookware Set',
                'description' =>
                    'Professional-grade 10-piece stainless steel cookware set featuring tri-ply construction with aluminum core for even heat distribution. Includes multiple sizes of pots and pans suitable for various cooking needs from sautéing to boiling. Features stay-cool handles, tight-fitting lids to lock in moisture and flavor, and dishwasher-safe construction. The polished stainless steel exterior resists stains and maintains its appearance over time. Compatible with all cooktops including induction, gas, electric, and ceramic. Perfect for home chefs who want durable, versatile cookware that performs well and lasts for years.',
                'category' => 'Home & Kitchen',
                'tags' => ['cookware','kitchen','stainless steel','cooking'],
                'price' => 199.99,
                'image_url' => 'https://picsum.photos/seed/cookware/600/400',
            ],

            [
                'name' => 'Smart Thermostat',
                'description' =>
                    'Wi-Fi enabled smart thermostat that learns your schedule and preferences to automatically adjust temperature for optimal comfort and energy savings. Features a large, easy-to-read color display with intuitive controls. Includes geofencing technology that detects when you leave or return home to adjust settings accordingly. Compatible with voice assistants for hands-free control and works with most HVAC systems. Provides detailed energy usage reports and tips for saving money. Can be controlled remotely via smartphone app from anywhere. Includes installation guide and professional installation support. Perfect for homeowners who want to reduce energy bills while maintaining comfort.',
                'category' => 'Home & Kitchen',
                'tags' => ['thermostat','smart home','energy','automation'],
                'price' => 179.99,
                'image_url' => 'https://picsum.photos/seed/thermostat/600/400',
            ],

            // Additional Toys Products
            [
                'name' => 'Remote Control Drone',
                'description' =>
                    'Advanced quadcopter drone with 4K camera and GPS positioning for stable flight and precise control. Features intelligent flight modes including follow-me, orbit, and waypoint navigation. Equipped with obstacle avoidance sensors, automatic return-to-home function, and a 3-axis gimbal for smooth, professional-quality video footage. Includes a controller with live video feed to your smartphone, multiple speed modes for beginners and experts, and a flight time of up to 30 minutes per battery. Comes with spare propellers, carrying case, and comprehensive instruction manual. Perfect for photography enthusiasts, hobbyists, and anyone interested in aerial videography and exploration.',
                'category' => 'Toys',
                'tags' => ['drone','remote control','camera','outdoor'],
                'price' => 299.99,
                'image_url' => 'https://picsum.photos/seed/drone/600/400',
            ],

            [
                'name' => 'LEGO Architecture Set',
                'description' =>
                    'Detailed LEGO Architecture building set featuring a famous landmark with over 2,000 pieces for an immersive building experience. Includes step-by-step instructions with historical information about the structure. Designed for ages 16+ with intricate details and realistic proportions. The finished model makes an impressive display piece for home or office. Encourages patience, attention to detail, and appreciation for architecture and engineering. Made from high-quality, durable LEGO bricks that connect securely. Perfect for LEGO enthusiasts, architecture lovers, and collectors who enjoy challenging building projects.',
                'category' => 'Toys',
                'tags' => ['lego','building','architecture','educational'],
                'price' => 149.99,
                'image_url' => 'https://picsum.photos/seed/lego/600/400',
            ],

            [
                'name' => 'Interactive Robot Toy',
                'description' =>
                    'Programmable interactive robot toy that responds to voice commands, gestures, and touch. Features multiple sensors including infrared, sound, and light detection for interactive play. Can be programmed using a simple visual programming interface or advanced coding for older children. Includes various modes: guard mode, patrol mode, dance mode, and story mode. Equipped with LED eyes that express emotions and a speaker for music and sound effects. Connects to smartphone app for extended features and remote control. Encourages STEM learning, creativity, and problem-solving skills. Perfect for children interested in robotics, programming, and technology.',
                'category' => 'Toys',
                'tags' => ['robot','interactive','programming','stem'],
                'price' => 79.99,
                'image_url' => 'https://picsum.photos/seed/robot-toy/600/400',
            ],

            [
                'name' => 'Board Game Collection',
                'description' =>
                    'Premium board game collection featuring three award-winning strategy games suitable for 2-6 players. Includes beautifully designed game boards, high-quality components, and detailed rulebooks. The games range from quick 30-minute sessions to immersive 2-hour experiences, offering variety for different occasions. Features cooperative and competitive gameplay modes, engaging themes, and strategic depth that appeals to both casual and serious gamers. Made from durable materials with attention to detail in artwork and design. Perfect for family game nights, parties, and gatherings with friends who enjoy social gaming experiences.',
                'category' => 'Toys',
                'tags' => ['board game','strategy','family','entertainment'],
                'price' => 59.99,
                'image_url' => 'https://picsum.photos/seed/boardgame/600/400',
            ],

            [
                'name' => 'RC Racing Car',
                'description' =>
                    'High-speed remote control racing car with 4WD system and powerful motor capable of reaching speeds up to 30 mph. Features proportional steering and throttle control for precise handling, LED headlights and taillights for night driving, and a durable chassis that can withstand crashes and jumps. Equipped with rechargeable battery providing 20-30 minutes of runtime and a controller with range up to 200 feet. Includes multiple speed modes for different skill levels and terrain types. Water-resistant design allows for outdoor use in various conditions. Perfect for racing enthusiasts, hobbyists, and anyone who enjoys high-speed RC vehicle action.',
                'category' => 'Toys',
                'tags' => ['rc car','racing','remote control','outdoor'],
                'price' => 89.99,
                'image_url' => 'https://picsum.photos/seed/rccar/600/400',
            ],

            // Additional Clothing Products
            [
                'name' => 'Men\'s Athletic Running Shoes',
                'description' =>
                    'Premium athletic running shoes engineered with advanced cushioning technology and breathable mesh upper for maximum comfort during long runs. Features responsive midsole foam that provides energy return with every step, reducing fatigue and enhancing performance. Includes durable rubber outsole with strategic traction patterns for grip on various surfaces. Designed with a supportive heel counter and secure lacing system for optimal fit. Lightweight construction reduces energy expenditure, while the breathable design keeps feet cool and dry. Suitable for road running, treadmill workouts, and daily training. Perfect for runners, athletes, and fitness enthusiasts who demand performance and comfort.',
                'category' => 'Clothing',
                'tags' => ['shoes','running','athletic','men'],
                'price' => 119.99,
                'image_url' => 'https://picsum.photos/seed/runningshoes/600/400',
            ],

            [
                'name' => 'Women\'s Winter Parka',
                'description' =>
                    'Insulated winter parka designed to withstand extreme cold weather conditions with premium down fill and water-resistant outer shell. Features a detachable faux fur hood, multiple pockets including interior security pockets, and adjustable cuffs and hem for customizable fit. Includes a two-way zipper with snap closure, insulated lining throughout, and a longer length for added protection. The durable construction and quality materials ensure warmth and protection in harsh winter conditions. Available in multiple colors and sizes. Perfect for cold climate living, winter sports, and anyone who needs reliable protection from freezing temperatures and wind.',
                'category' => 'Clothing',
                'tags' => ['jacket','winter','women','warm'],
                'price' => 199.99,
                'image_url' => 'https://picsum.photos/seed/parka/600/400',
            ],

            [
                'name' => 'Men\'s Denim Jeans',
                'description' =>
                    'Classic fit men\'s denim jeans made from premium stretch denim that combines comfort with durability. Features a traditional five-pocket design, reinforced stitching at stress points, and a comfortable waistband. The stretch fabric allows for freedom of movement while maintaining the classic denim look. Pre-washed for softness and to prevent shrinking. Available in multiple washes from dark indigo to light blue. Versatile design suitable for casual wear, work, and social occasions. Perfect for everyday wear, offering timeless style and lasting quality that gets better with age.',
                'category' => 'Clothing',
                'tags' => ['jeans','denim','men','casual'],
                'price' => 69.99,
                'image_url' => 'https://picsum.photos/seed/jeans/600/400',
            ],

            [
                'name' => 'Women\'s Summer Dress',
                'description' =>
                    'Elegant summer dress featuring a flowing silhouette with a flattering A-line cut that suits all body types. Made from lightweight, breathable fabric that keeps you cool in warm weather. Features a comfortable elastic waistband, adjustable straps, and a midi length that\'s perfect for various occasions. Includes a subtle floral pattern or solid color options, and a soft, wrinkle-resistant material that travels well. Versatile enough for casual outings, garden parties, or dressed up for evening events. Perfect for warm weather occasions, vacations, and anyone who wants effortless style and comfort.',
                'category' => 'Clothing',
                'tags' => ['dress','summer','women','casual'],
                'price' => 49.99,
                'image_url' => 'https://picsum.photos/seed/dress/600/400',
            ],

            [
                'name' => 'Unisex Hooded Sweatshirt',
                'description' =>
                    'Comfortable unisex hooded sweatshirt made from premium cotton blend fabric that\'s soft against the skin and durable for everyday wear. Features a roomy hood with drawstring closure, front kangaroo pocket for hands or storage, and ribbed cuffs and hem for a secure fit. The relaxed fit provides comfort and freedom of movement, while the quality construction ensures it maintains its shape after multiple washes. Available in a wide range of colors and sizes. Perfect for casual wear, lounging, outdoor activities, and layering during cooler weather. A wardrobe essential that combines comfort, style, and versatility.',
                'category' => 'Clothing',
                'tags' => ['sweatshirt','hoodie','unisex','casual'],
                'price' => 39.99,
                'image_url' => 'https://picsum.photos/seed/hoodie/600/400',
            ],

            [
                'name' => 'Men\'s Business Suit',
                'description' =>
                    'Professional men\'s business suit featuring a modern slim-fit design with premium wool blend fabric that drapes elegantly and maintains its shape. Includes a single-breasted jacket with notched lapels, matching dress pants with a flat front, and a classic color suitable for business and formal occasions. Features quality construction with attention to detail including functional buttonholes, interior pockets, and reinforced seams. The fabric is wrinkle-resistant and breathable for all-day comfort. Perfect for job interviews, business meetings, formal events, and professional settings where a polished appearance is essential.',
                'category' => 'Clothing',
                'tags' => ['suit','business','formal','men'],
                'price' => 299.99,
                'image_url' => 'https://picsum.photos/seed/suit/600/400',
            ],

            // Additional Books
            [
                'name' => 'Business Strategy: The Modern Approach',
                'description' =>
                    'A comprehensive guide to modern business strategy written by industry experts with decades of experience. Covers strategic planning, competitive analysis, market positioning, and organizational development. Includes real-world case studies from successful companies, frameworks for decision-making, and actionable strategies for growth. Explores topics such as digital transformation, customer-centric approaches, innovation management, and sustainable business practices. Features practical exercises, templates, and tools that readers can apply immediately. Perfect for business leaders, entrepreneurs, MBA students, and anyone looking to develop strategic thinking skills and drive business success in today\'s competitive landscape.',
                'category' => 'Books',
                'tags' => ['business','strategy','management','leadership'],
                'price' => 24.99,
                'image_url' => 'https://picsum.photos/seed/book11/600/400',
            ],

            [
                'name' => 'Programming Guide: Master Python',
                'description' =>
                    'Comprehensive programming guide for learning Python from beginner to advanced levels. Covers fundamental concepts, object-oriented programming, data structures, algorithms, and best practices. Includes hands-on projects, coding exercises, and real-world applications. Explores web development, data science, machine learning, and automation using Python. Features clear explanations, code examples, and troubleshooting tips. Suitable for aspiring programmers, students, and professionals looking to add Python to their skill set. Perfect for self-paced learning with structured chapters that build upon each other.',
                'category' => 'Books',
                'tags' => ['programming','python','technology','education'],
                'price' => 29.99,
                'image_url' => 'https://picsum.photos/seed/book12/600/400',
            ],

            [
                'name' => 'Travel Guide: Europe\'s Hidden Gems',
                'description' =>
                    'An extensive travel guide featuring off-the-beaten-path destinations across Europe that most tourists never discover. Includes detailed information about local culture, cuisine, accommodations, and transportation. Features stunning photography, maps, and insider tips from experienced travelers. Covers budget-friendly options, luxury experiences, and everything in between. Provides practical advice on visas, currency, language, and cultural etiquette. Includes suggested itineraries for different trip lengths and interests. Perfect for adventurous travelers, backpackers, and anyone looking to explore Europe beyond the typical tourist attractions.',
                'category' => 'Books',
                'tags' => ['travel','guide','europe','adventure'],
                'price' => 21.99,
                'image_url' => 'https://picsum.photos/seed/book13/600/400',
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
