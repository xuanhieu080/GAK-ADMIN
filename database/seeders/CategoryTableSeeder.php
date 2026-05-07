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

            // Tìm category hiện có theo tên
            $category = Category::where('name', $nameVi)->first();

            $data = [
                'name' => $nameVi,
                'name_en' => $nameEn,
                'slug' => $category ? $category->slug : $slug,
                'slug_en' => $category ? $category->slug_en : $slugEn,
                'is_active' => true,
                'show_header' => false,
                'show_dashboard' => false,
                'meta_title' => $nameVi,
                'meta_title_en' => $nameEn,
                'meta_description' => $nameVi,
                'meta_description_en' => $nameEn,
                'meta_key' => $nameVi,
                'meta_key_en' => $nameEn,
                'content_seo' => $nameVi,
                'content_seo_en' => $nameEn,
            ];

            if ($isBold) {
                // Tạo hoặc cập nhật category cha
                if ($category) {
                    $category->update($data);
                    $parentCategory = $category;
                    $this->command->info("Đã cập nhật danh mục cha: " . $nameVi);
                } else {
                    $parentCategory = Category::create($data);
                    $this->command->info("Đã tạo danh mục cha: " . $nameVi);
                }
            } else {
                // Tạo hoặc cập nhật category con
                if ($parentCategory) {
                    $data['parent_id'] = $parentCategory->id;
                }

                if ($category) {
                    $category->update($data);
                    $this->command->info("  - Đã cập nhật danh mục con: " . $nameVi);
                } else {
                    Category::create($data);
                    $this->command->info("  - Đã tạo danh mục con: " . $nameVi);
                }
            }
        }

        // Fix Nested Set boundaries
        Category::fixTree();
    }
}
