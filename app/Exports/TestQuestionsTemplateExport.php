<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TestQuestionsTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'x^2 = 4 tenglamaning musbat ildizi nechta?',
                '1',
                '2',
                '3',
                '4',
                2,
            ],
            [
                'Yorug\'lik tezligi taxminan qancha?',
                '150 000 km/s',
                '300 000 km/s',
                '450 000 km/s',
                '',
                2,
            ],
        ];
    }

    public function headings(): array
    {
        return ['savol', 'variant_1', 'variant_2', 'variant_3', 'variant_4', 'togri_variant'];
    }
}
