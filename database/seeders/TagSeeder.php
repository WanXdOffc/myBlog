<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Buat contoh tag awal untuk Tech Blog.
     */
    public function run(): void
    {
        $tags = [
            'Laravel',
            'PHP',
            'JavaScript',
            'TypeScript',
            'Vue.js',
            'React',
            'Node.js',
            'Python',
            'Docker',
            'Kubernetes',
            'AWS',
            'MySQL',
            'Redis',
            'REST API',
            'GraphQL',
            'Git',
            'Linux',
            'Tailwind CSS',
            'TDD',
            'Clean Code',
        ];

        foreach ($tags as $name) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                ]
            );
        }

        $this->command->info('✅ ' . count($tags) . ' tag berhasil dibuat.');
    }
}
