<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Collection;

class ImportProductsFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'products:import-excel {file?}';

    /**
     * The console command description.
     */
    protected $description = 'Import products from an Excel file and update the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Default file location if no path is passed
            $file = $this->argument('file') ?? storage_path('app/public/products.xlsx');

            if (!file_exists($file)) {
                $this->error("❌ File not found: $file");
                return Command::FAILURE;
            }

            $this->info("📂 Reading Excel file: $file");

            // Read Excel data (with first row as headings)
            $rows = Excel::toCollection(new class implements \Maatwebsite\Excel\Concerns\ToCollection, \Maatwebsite\Excel\Concerns\WithHeadingRow {
                public function collection(Collection $collection) {}
            }, $file)->first();

            if ($rows->isEmpty()) {
                $this->warn("⚠️ No data found in the Excel file.");
                return Command::SUCCESS;
            }

            $count = 0;

            foreach ($rows as $row) {
                $name = trim($row['name'] ?? '');
                $categoryName = trim($row['category'] ?? '');

                if (!$name || !$categoryName) {
                    continue; // skip incomplete rows
                }

                // Find or create the category
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    [
                        'slug' => Str::slug($categoryName),
                        'description' => $row['category_description'] ?? null
                    ]
                );

                // Handle tags (convert to JSON if not already)
                $tags = $row['tags'] ?? '[]';
                if (is_string($tags) && !str_starts_with(trim($tags), '[')) {
                    $tags = json_encode(array_map('trim', explode(',', $tags)));
                }

                // Create or update the product
                Product::updateOrCreate(
                    ['name' => $name],
                    [
                        'description' => $row['description'] ?? '',
                        'category_id' => $category->id,
                        'tags' => $tags,
                        'price' => (float)($row['price'] ?? 0),
                        'image_url' => $row['image_url'] ?? null,
                        'updated_at' => Carbon::now(),
                    ]
                );

                $count++;
            }

            $this->info("✅ Successfully imported/updated {$count} products.");
            return Command::SUCCESS;

        } catch (Throwable $th) {
            $this->error("❌ Error: " . $th->getMessage());
            return Command::FAILURE;
        }
    }
}
