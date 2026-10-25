<?php

declare(strict_types=1);

// Excel subject copy (EN). Same keys as ../utama.php.
return [
    'ringkas' => 'Formulas, lookups, conditional totals, and the shortcuts used every day in finance work.',
    'meta' => 'Cheat sheet, essential formulas, a searchable reference of every function, auto-checked exercises and an AI tutor for Microsoft Excel.',
    'alat' => [
        'cheatsheet' => 'Shortcuts, cell references, operators, and common mistakes on one page.',
        'formulas' => 'The formulas used most in finance work, with examples you can copy.',
        'reference' => 'Excel functions with syntax, an example, and the version that introduced each one. Searchable.',
        'practice' => 'Write formulas against real data. Answers are checked instantly, and equivalent formulas count.',
        'playground' => 'A grid in your browser that calculates formulas. Type one and see the result without opening Excel.',
        'tutor' => 'Ask about formulas, errors, or a screenshot of your sheet.',
    ],
    'popular' => ['SUM', 'IF', 'XLOOKUP', 'VLOOKUP', 'SUMIFS', 'COUNTIFS', 'IFERROR', 'INDEX', 'MATCH', 'AVERAGE', 'ROUND', 'TEXT', 'TEXTJOIN', 'DATE', 'EOMONTH', 'NETWORKDAYS', 'PMT', 'UNIQUE'],   // susunan = paparan /excel/
    'chat' => [
        'salam' => 'What would you like to know about Excel?',
        'intro' => 'Ask about formulas, functions, errors or how to structure your data. The tutor answers in the language you ask in.',
        'lampiran' => 'You can attach a screenshot of your sheet.',
        'cadangan' => ['What is the difference between VLOOKUP and XLOOKUP?', 'How do I use SUMIFS with two criteria?', 'When do I need $ in a formula?', 'How do I calculate month-on-month growth?'],
        'placeholder' => 'Ask about Excel',
        'meta' => 'AI tutor for Microsoft Excel: formulas, functions, errors, and data structure, in English or Malay.',
        'nota' => 'AI can be wrong. Test formulas in Excel before you rely on them.',
        'nota_gambar' => 'Images are resized in your browser before sending. AI can be wrong. Test formulas in Excel.',
    ],
];
