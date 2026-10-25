<?php

declare(strict_types=1);

/**
 * Word cheat sheet (/en/word/cheatsheet), EN. Same structure as ../cheatsheet.php (BM). Schema: docs/REVAMP.md.
 * Shortcuts = Word for Microsoft 365 (Windows).
 */
return [
    'lencana' => 'Word cheat sheet',
    'tajuk' => 'Word cheat sheet',
    'intro' => 'Shortcuts, styles, page layout, long documents, and the mistakes that most often break a work document.',
    'bahagian' => [
        [
            'id' => 'shortcuts', 'ikon' => 'keyboard', 'tajuk' => 'Everyday shortcuts', 'jenis' => 'pintasan',
            'sub' => 'Word for Microsoft 365 on Windows. Some Mac shortcuts differ.',
            'item' => [
                ['keys' => ['Ctrl', 'B'], 'name' => 'Bold', 'desc' => 'Make the selected text bold.'],
                ['keys' => ['Ctrl', 'I'], 'name' => 'Italic', 'desc' => 'Italicize text.'],
                ['keys' => ['Ctrl', 'U'], 'name' => 'Underline', 'desc' => 'Underline text.'],
                ['keys' => ['Ctrl', 'E'], 'name' => 'Center', 'desc' => 'Center the paragraph.'],
                ['keys' => ['Ctrl', 'L'], 'name' => 'Align Left', 'desc' => 'Align the paragraph to the left.'],
                ['keys' => ['Ctrl', 'J'], 'name' => 'Justify', 'desc' => 'Align to both margins, common in formal letters.'],
                ['keys' => ['Ctrl', 'Enter'], 'name' => 'Page Break', 'desc' => 'Start a new page. Do not press Enter repeatedly.'],
                ['keys' => ['Shift', 'Enter'], 'name' => 'Line Break', 'desc' => 'New line within the same paragraph.'],
                ['keys' => ['Ctrl', 'Alt', '1'], 'name' => 'Heading 1', 'desc' => 'Main heading style. The table of contents is built from it.'],
                ['keys' => ['Ctrl', 'Alt', '2'], 'name' => 'Heading 2', 'desc' => 'Subheading style.'],
                ['keys' => ['Ctrl', 'Shift', 'N'], 'name' => 'Normal', 'desc' => 'Return the paragraph to the Normal style.'],
                ['keys' => ['Ctrl', 'Alt', 'C'], 'name' => 'Copy Formatting', 'desc' => 'Copy formatting. Paste it with <strong>Ctrl+Alt+V</strong>. Word 2019 and earlier: Ctrl+Shift+C and Ctrl+Shift+V.'],
                ['keys' => ['Ctrl', 'Shift', 'V'], 'name' => 'Paste Text Only', 'desc' => 'Paste text without the source formatting (Microsoft 365).'],
                ['keys' => ['Ctrl', 'Space'], 'name' => 'Clear Character Formatting', 'desc' => 'Remove manual character formatting such as bold, color, and size.'],
                ['keys' => ['Shift', 'F3'], 'name' => 'Change Case', 'desc' => 'Cycle between lowercase, UPPERCASE, and Capitalize Each Word.'],
                ['keys' => ['Ctrl', 'K'], 'name' => 'Hyperlink', 'desc' => 'Insert a link.'],
                ['keys' => ['Ctrl', 'H'], 'name' => 'Replace', 'desc' => 'Find and replace text, formatting, or special characters.'],
                ['keys' => ['Ctrl', 'Shift', '8'], 'name' => 'Show/Hide ¶', 'desc' => 'Show or hide paragraph marks, tabs, and spaces.'],
                ['keys' => ['F7'], 'name' => 'Editor', 'desc' => 'Check spelling and grammar.'],
                ['keys' => ['Ctrl', 'Alt', 'M'], 'name' => 'New Comment', 'desc' => 'Add a comment to the selected text.'],
                ['keys' => ['Ctrl', 'Shift', 'E'], 'name' => 'Track Changes', 'desc' => 'Turn Track Changes on or off.'],
            ],
        ],
        [
            'id' => 'styles', 'ikon' => 'heading', 'tajuk' => 'Styles for consistent documents', 'jenis' => 'kad',
            'sub' => 'Change a style once and all text that uses it updates. Manual formatting has to be changed one place at a time.',
            'item' => [
                ['Heading 1, 2, 3', 'Use them for chapter titles and subheadings. The Navigation Pane (Ctrl+F) and the table of contents are built from them.'],
                ['Normal', 'The style for body text. To change the font or size for the whole document: Home → Styles → right-click Normal → Modify.'],
                ['Modify Style', 'Right-click a style name in Home → Styles → Modify. Leave <em>Automatically update</em> cleared, or the style changes every time you format text by hand.'],
                ['Update to Match Selection', 'Format one heading first, then right-click Heading 1 → Update Heading 1 to Match Selection.'],
            ],
        ],
        [
            'id' => 'breaks', 'ikon' => 'between-horizontal-start', 'tajuk' => 'Types of breaks', 'jenis' => 'jadual',
            'sub' => 'Choose the break based on what needs to change after it.',
            'kepala' => ['Break', 'How', 'Use it for'],
            'baris' => [
                ['Line Break', '<strong>Shift+Enter</strong>', 'A new line in the same paragraph, such as an address in a letter.'],
                ['Page Break', '<strong>Ctrl+Enter</strong> or Insert → Page Break', 'Start a new page. Page settings stay the same.'],
                ['Column Break', '<strong>Ctrl+Shift+Enter</strong> or Layout → Breaks → Column', 'Move text to the next column in a multi-column layout.'],
                ['Section Break (Next Page)', 'Layout → Breaks → Next Page', 'A new page whose orientation, margins, header, or page numbers can differ.'],
                ['Section Break (Continuous)', 'Layout → Breaks → Continuous', 'A new section on the same page, such as two columns for part of a page.'],
            ],
        ],
        [
            'id' => 'page-layout', 'ikon' => 'ruler', 'tajuk' => 'Page layout', 'jenis' => 'kad',
            'sub' => 'The Layout tab controls paper size, margins, orientation, and breaks.',
            'item' => [
                ['One landscape page in the middle of a document', 'Insert a Section Break (Next Page) before and after that page. Then click in the page and select Layout → Orientation → Landscape.'],
                ['Margins and paper size', 'Layout → Margins and Layout → Size. Most official documents in Malaysia use A4.'],
                ['Page numbers', 'Insert → Page Number. To start at a different number: Page Number → Format Page Numbers → Start at.'],
                ['Headers and footers', 'Insert → Header or Footer, or double-click the top of the page. Turn off <em>Link to Previous</em> to give each section its own header.'],
            ],
        ],
        [
            'id' => 'long-documents', 'ikon' => 'book-open', 'tajuk' => 'Long reports', 'jenis' => 'kad',
            'sub' => 'The References tab. Word keeps all of these up to date, so do not type them by hand.',
            'item' => [
                ['Table of Contents', 'References → Table of Contents. Headings must use Heading 1, 2, or 3. After editing: click in the table of contents → Update Table, or press F9.'],
                ['Caption', 'Right-click a picture or table → Insert Caption. Numbers such as Figure 1, 2, 3 are generated automatically.'],
                ['Cross-reference', 'References → Cross-reference. A reference such as "see Figure 3" updates when the figure moves.'],
                ['Footnote', 'References → Insert Footnote (Ctrl+Alt+F). Footnote numbers renumber automatically.'],
            ],
        ],
        [
            'id' => 'mistakes', 'ikon' => 'triangle-alert', 'tajuk' => 'Common mistakes', 'jenis' => 'kesilapan',
            'sub' => 'Avoid these and your documents stay tidy and easy to edit.',
            'item' => [
                ['Pressing Enter repeatedly to reach a new page', 'When text above grows, everything shifts and the layout breaks. <strong>Fix:</strong> Ctrl+Enter (Page Break).'],
                ['Pressing Space or Tab repeatedly to line up text', 'It looks aligned on your screen and crooked on another computer. <strong>Fix:</strong> use paragraph alignment, tab stops on the ruler, or a table without borders.'],
                ['Formatting headings by hand (bold and a larger size)', 'The table of contents and the Navigation Pane do not recognize them. <strong>Fix:</strong> use the Heading 1, 2, or 3 styles.'],
                ['Typing page numbers or the table of contents yourself', 'The numbers are wrong as soon as the document changes. <strong>Fix:</strong> Insert → Page Number and References → Table of Contents.'],
                ['Sending a document that still contains tracked changes', 'The recipient can see every edit and comment. <strong>Fix:</strong> Review → Accept All Changes, delete the comments, then File → Info → Check for Issues → Inspect Document.'],
            ],
            'amalan' => [
                'Turn on Show/Hide ¶ (Ctrl+Shift+8) when the layout looks wrong. Hidden Enters, tabs, and breaks become visible.',
                'Save final documents as PDF (File → Save As → PDF) so the layout does not change on another computer.',
                'Save files to OneDrive to get AutoSave and Version History.',
                'Use templates (File → New) for letters and reports you write often.',
            ],
        ],
    ],
];
