<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Science;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScienceGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sciences = [
            ['title' => 'Ona tili', 'icon' => 'bi-book', 'color' => '#2b5fa8'],
            ['title' => 'Adabiyot', 'icon' => 'bi-journal-richtext', 'color' => '#4b3a86'],
            ['title' => 'Fizika', 'icon' => 'bi-lightning-charge', 'color' => '#a4761b'],
            ['title' => 'Matematika', 'icon' => 'bi-calculator', 'color' => '#1f5f57'],
            ['title' => 'Kimyo', 'icon' => 'bi-droplet-half', 'color' => '#9d3242'],
            ['title' => 'Biologiya', 'icon' => 'bi-tree', 'color' => '#2f6f5f'],
            ['title' => 'Geografiya', 'icon' => 'bi-globe-americas', 'color' => '#2b5fa8'],
            ['title' => 'Ingliz tili', 'icon' => 'bi-translate', 'color' => '#2b5fa8'],
            ['title' => 'Rus tili', 'icon' => 'bi-translate', 'color' => '#9d3242'],
            ['title' => 'Nemis tili', 'icon' => 'bi-translate', 'color' => '#a4761b'],
            ['title' => 'Fransuz tili', 'icon' => 'bi-translate', 'color' => '#4b3a86'],
            ['title' => 'Tarix', 'icon' => 'bi-bank', 'color' => '#6b7489'],
            ['title' => 'Huquq', 'icon' => 'bi-shield-check', 'color' => '#2c3448'],
            ['title' => 'Dasturlash', 'icon' => 'bi-code-slash', 'color' => '#5B6BC0'],
            ['title' => 'Robototexnika', 'icon' => 'bi-cpu', 'color' => '#3A8DAD'],
            ['title' => 'Grafik dizayn', 'icon' => 'bi-palette', 'color' => '#C2588D'],
        ];

        foreach ($sciences as $position => $s) {
            Science::updateOrCreate(
                ['title' => $s['title']],
                ['icon' => $s['icon'], 'color' => $s['color']]
            );
        }

        foreach (range(1, 11) as $i) {
            Grade::firstOrCreate(['title' => $i . '-sinf']);
        }
        Grade::create(['title' => 'boshqa']);
    }
}
