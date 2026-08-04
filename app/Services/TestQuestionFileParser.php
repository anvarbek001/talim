<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class TestQuestionFileParser
{
    public function __construct(
        protected TestQuestionExcelParser $excelParser,
        protected TestQuestionWordParser $wordParser,
    ) {}

    /**
     * Picks the Excel or Word parser based on the uploaded file's extension.
     *
     * @return array<int, array{text: string, options: array<int, array{text: string}>, correct: int}>
     */
    public function parse(UploadedFile $file): array
    {
        return $this->isWord($file)
            ? $this->wordParser->parse($file)
            : $this->excelParser->parse($file);
    }

    protected function isWord(UploadedFile $file): bool
    {
        return strtolower($file->getClientOriginalExtension()) === 'docx';
    }
}
