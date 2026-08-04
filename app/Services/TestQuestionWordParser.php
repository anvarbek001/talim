<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Text as WordText;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class TestQuestionWordParser
{
    /**
     * Parses a .docx file into the same questions[] shape the Excel parser
     * produces. Expected format, one line (paragraph) per entry:
     *
     *   1. Savol matni?
     *   A) Variant 1
     *   B) Variant 2 *      <- trailing * marks the correct option
     *   C) Variant 3
     *   D) Variant 4
     *
     * @return array<int, array{text: string, options: array<int, array{text: string}>, correct: int}>
     */
    public function parse(UploadedFile $file): array
    {
        $phpWord = IOFactory::load($file->getRealPath(), 'Word2007');

        $questions = [];
        $current = null;

        foreach ($this->paragraphs($phpWord) as $line) {
            if (preg_match('/^\d+[.\)]\s*(.+)$/u', $line, $matches)) {
                if ($current !== null) {
                    $questions[] = $this->finalizeQuestion($current);
                }

                $current = ['text' => trim($matches[1]), 'options' => []];

                continue;
            }

            if ($current !== null && preg_match('/^[A-Za-z][.\)]\s*(.+)$/u', $line, $matches)) {
                $current['options'][] = trim($matches[1]);
            }
        }

        if ($current !== null) {
            $questions[] = $this->finalizeQuestion($current);
        }

        return array_values(array_filter($questions));
    }

    /**
     * @param  array{text: string, options: array<int, string>}  $question
     * @return array{text: string, options: array<int, array{text: string}>, correct: int}|null
     */
    protected function finalizeQuestion(array $question): ?array
    {
        $options = [];
        $correct = 0;

        foreach ($question['options'] as $optionText) {
            $isCorrect = (bool) preg_match('/\*\s*$/', $optionText);
            $cleanText = trim((string) preg_replace('/\*\s*$/', '', $optionText));

            if ($cleanText === '') {
                continue;
            }

            if ($isCorrect) {
                $correct = count($options);
            }

            $options[] = ['text' => $cleanText];
        }

        if ($question['text'] === '' || count($options) < 2) {
            return null;
        }

        return [
            'text' => $question['text'],
            'options' => $options,
            'correct' => $correct,
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function paragraphs(PhpWord $phpWord): array
    {
        $lines = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text = trim($this->elementText($element));

                if ($text !== '') {
                    $lines[] = $text;
                }
            }
        }

        return $lines;
    }

    protected function elementText(mixed $element): string
    {
        if ($element instanceof WordText) {
            // PhpWord's writer HTML-escapes text runs (e.g. ' -> &#039;) even
            // though OOXML text nodes don't require it, so undo that on read.
            return html_entity_decode((string) $element->getText(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if ($element instanceof AbstractContainer) {
            $parts = [];
            foreach ($element->getElements() as $child) {
                $parts[] = $this->elementText($child);
            }

            return implode('', $parts);
        }

        return '';
    }
}
