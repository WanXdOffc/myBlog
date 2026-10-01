<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Buat contoh kategori awal untuk Tech Blog.
     */
    public function run(): void
    {
        $categories = [
            'Web Development',
            'Mobile Development',
            'DevOps & Cloud',
            'Artificial Intelligence',
            'Cybersecurity',
            'Programming Tips',
            'Open Source',
            'Tutorial',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                ]
            );
        }

        $this->command->info('✅ ' . count($categories) . ' kategori berhasil dibuat.');
    }
}
