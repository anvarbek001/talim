<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;

class TestQuestionsWordTemplateBuilder
{
    /**
     * A sample .docx showing the expected Word import format: a numbered
     * question line, lettered option lines, and a trailing "*" marking the
     * correct option.
     */
    public function build(): PhpWord
    {
        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $section->addText(
            "Savollar shabloni — har bir savolni raqam bilan (1. 2. 3. ...), variantlarni harf bilan (A) B) C) D)) yozing. To'g'ri javob variantining oxiriga * belgisini qo'ying."
        );
        $section->addTextBreak(1);

        $section->addText('1. x^2 = 4 tenglamaning musbat ildizi nechta?');
        $section->addText('A) 1');
        $section->addText('B) 2 *');
        $section->addText('C) 3');
        $section->addText('D) 4');
        $section->addTextBreak(1);

        $section->addText("2. Yorug'lik tezligi taxminan qancha?");
        $section->addText('A) 150 000 km/s');
        $section->addText('B) 300 000 km/s *');
        $section->addText('C) 450 000 km/s');

        return $phpWord;
    }
}
