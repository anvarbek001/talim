<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class TestQuestionExcelParser
{
    /**
     * Parse an uploaded xlsx/xls file into the questions[] array shape
     * consumed by TopicTestService/DtmTestService/SertifikatTestService.
     *
     * Expected columns: savol | variant_1 | variant_2 | variant_3 | variant_4 | togri_variant
     *
     * @return array<int, array{text: string, options: array<int, array{text: string}>, correct: int}>
     */
    public function parse(UploadedFile $file): array
    {
        $sheets = Excel::toCollection(null, $file);
        $rows = $sheets->first() ?? collect();

        $questions = [];

        foreach ($rows->skip(1) as $row) {
            $parsed = $this->parseRow($row->toArray());

            if ($parsed !== null) {
                $questions[] = $parsed;
            }
        }

        return $questions;
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array{text: string, options: array<int, array{text: string}>, correct: int}|null
     */
    protected function parseRow(array $row): ?array
    {
        $questionText = trim((string) ($row[0] ?? ''));

        if ($questionText === '') {
            return null;
        }

        $options = [];
        foreach ([1, 2, 3, 4] as $column) {
            $text = trim((string) ($row[$column] ?? ''));

            if ($text === '') {
                continue;
            }

            $options[] = ['text' => $text, 'column' => $column];
        }

        if (count($options) < 2) {
            return null;
        }

        $correctColumn = (int) ($row[5] ?? 0);
        $correctIndex = 0;

        foreach ($options as $index => $option) {
            if ($option['column'] === $correctColumn) {
                $correctIndex = $index;
                break;
            }
        }

        return [
            'text' => $questionText,
            'options' => array_map(fn (array $option) => ['text' => $option['text']], $options),
            'correct' => $correctIndex,
        ];
    }
}
