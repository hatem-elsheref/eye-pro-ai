<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dictionary;
use Illuminate\Support\Facades\DB;

class DictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('dictionary')->truncate();

        // Offence Severity entries
        $offenceSeverity = [
            ['en' => 'Tackling', 'ar' => 'تدخل'],
            ['en' => 'Standing tackling', 'ar' => 'تدخل واقف'],
            ['en' => 'High leg', 'ar' => 'رجل عالية'],
            ['en' => 'Holding', 'ar' => 'إمساك'],
            ['en' => 'Pushing', 'ar' => 'دفع'],
            ['en' => 'Elbowing', 'ar' => 'ضربة بالكوع'],
            ['en' => 'Challenge', 'ar' => 'تحدي'],
            ['en' => 'Dive', 'ar' => 'غوص'],
            ['en' => "Don't know", 'ar' => 'لا أعرف'],
        ];

        foreach ($offenceSeverity as $index => $item) {
            Dictionary::create([
                'type' => 'offence_severity',
                'class' => $index, // 0-8
                'label_en' => $item['en'],
                'label_ar' => $item['ar'],
                'description_en' => $item['en'],
                'description_ar' => $item['ar'],
            ]);
        }

        // Action entries
        $actions = [
            ['en' => 'No offence', 'ar' => 'لا توجد مخالفة'],
            ['en' => 'Offence + No card', 'ar' => 'مخالفة بدون بطاقة'],
            ['en' => 'Offence + Yellow card', 'ar' => 'مخالفة + بطاقة صفراء'],
            ['en' => 'Offence + Red card', 'ar' => 'مخالفة + بطاقة حمراء'],
        ];

        foreach ($actions as $index => $item) {
            Dictionary::create([
                'type' => 'action',
                'class' => $index, // 0-3
                'label_en' => $item['en'],
                'label_ar' => $item['ar'],
                'description_en' => $item['en'],
                'description_ar' => $item['ar'],
            ]);
        }

        $this->command->info('Dictionary seeded successfully!');
        $this->command->info('Created ' . count($offenceSeverity) . ' offence severity entries');
        $this->command->info('Created ' . count($actions) . ' action entries');
    }
}
