<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = public_path('Danh mục.xlsx');
        if (!file_exists($filePath)) {
            $this->command->error("File không tồn tại: {$filePath}");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        $parentCategory = null;

        // Bỏ qua dòng tiêu đề (dòng 1)
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex == 1) continue;

            $nameEn = trim($row['A'] ?? '');
            $nameVi = trim($row['B'] ?? '');

            if (empty($nameEn) && empty($nameVi)) continue;

            $isBold = false;
            try {
                $isBold = $worksheet->getStyle('A' . $rowIndex)->getFont()->getBold();
            } catch (\Exception $e) {
                // Ignore error if cannot get style
            }

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
