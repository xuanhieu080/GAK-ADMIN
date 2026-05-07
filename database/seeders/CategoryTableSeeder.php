<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = public_path('categories_data.json');
        if (!file_exists($jsonPath)) {
            $this->command->error("File không tồn tại: {$jsonPath}");
            return;
        }

        $dataJson = file_get_contents($jsonPath);
        $rows = json_decode($dataJson, true);

        $parentCategory = null;

        // Bỏ qua dòng tiêu đề (dòng 0 trong JSON)
        foreach ($rows as $index => $row) {
            if ($index == 0) continue;

            $nameEn = trim($row[0] ?? '');
            $nameVi = trim($row[1] ?? '');
            $isBold = (bool)($row[2] ?? false);

            if (empty($nameEn) && empty($nameVi)) continue;

            $slug = Str::slug($nameVi);
            $slugEn = Str::slug($nameEn);

            // Xử lý trùng slug bằng cách thêm hậu tố số nếu cần
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $originalSlugEn = $slugEn;
            $countEn = 1;
            while (Category::where('slug_en', $slugEn)->exists()) {
                $slugEn = $originalSlugEn . '-' . $countEn++;
            }

            $data = [
                'name' => $nameVi,
                'name_en' => $nameEn,
                'slug' => $slug,
                'slug_en' => $slugEn,
                'is_active' => true,
                'show_header' => true,
                'show_dashboard' => true,
            ];

            if ($isBold) {
                // Tạo category cha
                $parentCategory = Category::create($data);
                $this->command->info("Đã tạo danh mục cha: " . $nameVi);
            } else {
                // Tạo category con
                if ($parentCategory) {
                    $data['parent_id'] = $parentCategory->id;
                }
                Category::create($data);
                $this->command->info("  - Đã tạo danh mục con: " . $nameVi);
            }
        }

        // Fix Nested Set boundaries
        Category::fixTree();
    }
}
