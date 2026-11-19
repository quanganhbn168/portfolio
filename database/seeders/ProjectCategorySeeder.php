<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

final class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Website Doanh nghiệp', 'Landing Page', 'Du lịch - Dịch vụ', 'Thương mại', 'Phần mềm'] as $index => $name) {
            ProjectCategory::query()->updateOrCreate(['name' => $name], [
                'parent_id' => null, 'is_active' => true, 'sort_order' => ($index + 1) * 10,
            ]);
        }
    }
}
