<?php

declare(strict_types=1);

/**
 * Word feature reference (/en/word/reference), EN. Same structure as ../rujukan.php (BM): nama, cat, pintasan identical,
 * same order. Ribbon paths = Word for Microsoft 365 (Windows). The current view puts the feature count before `intro`.
 */
return [
    'tajuk' => 'Word feature reference',
    'intro' => 'essential Word features, with ribbon paths, shortcuts, and tips for work documents.',
    'kategori' => [
        'format' => ['Formatting', 'type'],
        'gaya' => ['Styles and structure', 'heading'],
        'halaman' => ['Pages', 'ruler'],
        'jadual' => ['Tables and pictures', 'table'],
        'laporan' => ['Reports', 'book-open'],
        'semakan' => ['Review', 'pen-line'],
        'mel' => ['Mail Merge', 'mail'],
        'fail' => ['Save and share', 'save'],
    ],
    'ciri' => [
        // === FORMATTING ===
        ['nama' => 'Format Painter', 'cat' => 'format', 'popular' => true, 'desc' => 'Copy formatting from one piece of text to another.', 'langkah' => 'Select the source text → Home → Format Painter → select the target text.', 'pintasan' => 'Ctrl+Alt+C / Ctrl+Alt+V', 'tip' => 'Double-click Format Painter to apply it several times, then press Esc. Word 2019 and earlier: Ctrl+Shift+C and Ctrl+Shift+V.'],
        ['nama' => 'Clear Formatting', 'cat' => 'format', 'desc' => 'Remove formatting from text and return it to its base style.', 'langkah' => 'Select text → Home → Clear All Formatting (the letter A with an eraser).', 'pintasan' => 'Ctrl+Space', 'tip' => 'The Clear All Formatting button removes all formatting. Ctrl+Space removes manual character formatting only. Text pasted from the web often carries stray formatting, so clear it first.'],
        ['nama' => 'Paste: Keep Text Only', 'cat' => 'format', 'popular' => true, 'desc' => 'Paste text without the source formatting.', 'langkah' => 'Home → Paste (down arrow) → Keep Text Only.', 'pintasan' => 'Ctrl+Shift+V', 'tip' => 'This shortcut is for Microsoft 365. To set the default paste behavior: File → Options → Advanced → Cut, copy, and paste.'],
        ['nama' => 'Line & Paragraph Spacing', 'cat' => 'format', 'desc' => 'Space between lines and between paragraphs.', 'langkah' => 'Home → Line and Paragraph Spacing (1.0, 1.15, 1.5, 2.0). Space between paragraphs: Layout → Spacing Before and After.', 'pintasan' => 'Ctrl+1 / Ctrl+5 / Ctrl+2', 'tip' => 'Ctrl+1 = single, Ctrl+5 = 1.5 lines, Ctrl+2 = double. Use Spacing After, not empty paragraphs, to separate paragraphs.'],
        ['nama' => 'Change Case', 'cat' => 'format', 'desc' => 'Switch to lowercase, UPPERCASE, or Capitalize Each Word without retyping.', 'langkah' => 'Select text → Home → Change Case (Aa).', 'pintasan' => 'Shift+F3'],
        ['nama' => 'Bullets & Numbering', 'cat' => 'format', 'popular' => true, 'desc' => 'Bulleted or numbered lists that renumber automatically.', 'langkah' => 'Home → Bullets or Numbering. To restart at 1: right-click the number → Restart at 1.', 'tip' => 'At the start of a list item, Tab demotes one level and Shift+Tab promotes one level.'],
        ['nama' => 'Find & Replace', 'cat' => 'format', 'popular' => true, 'desc' => 'Find and replace text, including formatting and special characters.', 'langkah' => 'Home → Replace. Click More >> for the Format and Special options (for example, <code>^p</code> = paragraph mark).', 'pintasan' => 'Ctrl+H', 'tip' => 'To remove doubled empty paragraphs: Find what <code>^p^p</code>, Replace with <code>^p</code>.'],

        // === STYLES AND STRUCTURE ===
        ['nama' => 'Styles (Heading 1/2/3)', 'cat' => 'gaya', 'popular' => true, 'desc' => 'Heading styles that drive the table of contents, the Navigation Pane, and chapter numbering.', 'langkah' => 'Select a heading → Home → Styles → Heading 1.', 'pintasan' => 'Ctrl+Alt+1 / 2 / 3', 'tip' => 'To change the look of every heading at once: right-click Heading 1 → Modify.'],
        ['nama' => 'Modify Style', 'cat' => 'gaya', 'desc' => 'Change the font, size, color, or spacing of a style. The whole document follows.', 'langkah' => 'Home → Styles → right-click the style → Modify → Format (Font or Paragraph).'],
        ['nama' => 'Navigation Pane', 'cat' => 'gaya', 'popular' => true, 'desc' => 'A map of the document: jump to a heading, or drag headings to reorder chapters.', 'langkah' => 'View → Navigation Pane (Headings tab).', 'pintasan' => 'Ctrl+F', 'tip' => 'If a heading is missing, it does not use a Heading style yet.'],
        ['nama' => 'Multilevel List', 'cat' => 'gaya', 'desc' => 'Chapter numbering such as 1, 1.1, 1.1.1 linked to the Heading styles.', 'langkah' => 'Home → Multilevel List → choose the list that shows "Heading 1, Heading 2".'],
        ['nama' => 'Show/Hide ¶', 'cat' => 'gaya', 'desc' => 'Show hidden paragraph marks, tabs, spaces, and breaks.', 'langkah' => 'Home → ¶.', 'pintasan' => 'Ctrl+Shift+8', 'tip' => 'The first thing to check when a layout looks wrong.'],

        // === PAGES ===
        ['nama' => 'Page Break', 'cat' => 'halaman', 'popular' => true, 'desc' => 'Start a new page at the cursor.', 'langkah' => 'Insert → Page Break, or Layout → Breaks → Page.', 'pintasan' => 'Ctrl+Enter'],
        ['nama' => 'Section Break', 'cat' => 'halaman', 'popular' => true, 'desc' => 'Divide the document so orientation, margins, headers, or page numbers can differ.', 'langkah' => 'Layout → Breaks → Next Page (or Continuous).', 'tip' => 'For one landscape page in the middle of a document: a section break before and after that page, then Layout → Orientation → Landscape.'],
        ['nama' => 'Margins, Orientation, Size', 'cat' => 'halaman', 'desc' => 'Page margins, orientation, and paper size.', 'langkah' => 'Layout → Margins, Orientation, or Size.'],
        ['nama' => 'Header & Footer', 'cat' => 'halaman', 'popular' => true, 'desc' => 'Text that repeats at the top or bottom of every page.', 'langkah' => 'Insert → Header or Footer, or double-click the top of the page.', 'tip' => 'For a different header in one section: turn off Link to Previous on the Header & Footer tab.'],
        ['nama' => 'Page Number', 'cat' => 'halaman', 'popular' => true, 'desc' => 'Automatic page numbers, including i, ii, iii for front matter.', 'langkah' => 'Insert → Page Number → choose a position. Format: Page Number → Format Page Numbers.', 'tip' => 'For a cover page without a number: Header & Footer tab → Different First Page.'],
        ['nama' => 'Columns', 'cat' => 'halaman', 'desc' => 'Text in two or three columns, as in a newsletter.', 'langkah' => 'Layout → Columns. To apply columns to part of the text only, select that text first.', 'pintasan' => 'Ctrl+Shift+Enter (Column Break)'],
        ['nama' => 'Watermark', 'cat' => 'halaman', 'desc' => 'Faded text behind the page, such as DRAFT or CONFIDENTIAL.', 'langkah' => 'Design → Watermark → Custom Watermark.'],

        // === TABLES AND PICTURES ===
        ['nama' => 'Insert Table', 'cat' => 'jadual', 'popular' => true, 'desc' => 'A table for data and for layouts that need to line up.', 'langkah' => 'Insert → Table → choose the number of rows and columns.', 'tip' => 'Press Tab in the last cell to add a new row.'],
        ['nama' => 'Repeat Header Rows', 'cat' => 'jadual', 'desc' => 'Repeat the table header row on every page.', 'langkah' => 'Select the header row → Table Layout → Repeat Header Rows.', 'tip' => 'Repeated header rows appear only in Print Layout view and when you print.'],
        ['nama' => 'Convert Text to Table', 'cat' => 'jadual', 'desc' => 'Turn text separated by tabs or commas into a table.', 'langkah' => 'Select the text → Insert → Table → Convert Text to Table.'],
        ['nama' => 'Wrap Text', 'cat' => 'jadual', 'popular' => true, 'desc' => 'How text flows around a picture.', 'langkah' => 'Select the picture → Picture Format → Wrap Text (In Line with Text, Square, Tight, and others).', 'tip' => 'If pictures move around while you edit, use In Line with Text for reports.'],
        ['nama' => 'Compress Pictures', 'cat' => 'jadual', 'desc' => 'Reduce the file size of a document with many pictures.', 'langkah' => 'Select a picture → Picture Format → Compress Pictures.'],

        // === REPORTS ===
        ['nama' => 'Table of Contents', 'cat' => 'laporan', 'popular' => true, 'desc' => 'An automatic table of contents with page numbers.', 'langkah' => 'References → Table of Contents → Automatic Table 1. To update: References → Update Table, or click in the table of contents and press F9.', 'pintasan' => 'F9', 'tip' => 'Headings must use the Heading 1, 2, or 3 styles.'],
        ['nama' => 'Insert Caption', 'cat' => 'laporan', 'desc' => 'Numbered labels for figures and tables.', 'langkah' => 'Right-click a picture or table → Insert Caption, or References → Insert Caption.', 'tip' => 'List of figures: References → Insert Table of Figures.'],
        ['nama' => 'Cross-reference', 'cat' => 'laporan', 'desc' => 'A reference such as "see Figure 3" that updates automatically.', 'langkah' => 'References → Cross-reference → choose the type (Figure, Table, Heading).'],
        ['nama' => 'Footnote', 'cat' => 'laporan', 'desc' => 'A numbered note at the bottom of the page.', 'langkah' => 'References → Insert Footnote.', 'pintasan' => 'Ctrl+Alt+F'],
        ['nama' => 'Citation & Bibliography', 'cat' => 'laporan', 'desc' => 'Source citations and a bibliography (APA and other styles).', 'langkah' => 'References → Insert Citation → Add New Source. The list: References → Bibliography.'],

        // === REVIEW ===
        ['nama' => 'Track Changes', 'cat' => 'semakan', 'popular' => true, 'desc' => 'Record every edit so it can be accepted or rejected.', 'langkah' => 'Review → Track Changes. To accept: Review → Accept or Accept All Changes.', 'pintasan' => 'Ctrl+Shift+E'],
        ['nama' => 'Comments', 'cat' => 'semakan', 'desc' => 'Comments in the margin that leave the text unchanged.', 'langkah' => 'Select text → Review → New Comment.', 'pintasan' => 'Ctrl+Alt+M'],
        ['nama' => 'Compare', 'cat' => 'semakan', 'desc' => 'Compare two versions of a document and see the differences.', 'langkah' => 'Review → Compare → Compare → choose the original and the revised document.'],
        ['nama' => 'Editor', 'cat' => 'semakan', 'desc' => 'Check spelling and grammar.', 'langkah' => 'Review → Editor (or Spelling & Grammar). Language: Review → Language → Set Proofing Language.', 'pintasan' => 'F7', 'tip' => 'For a document in Malay, set the proofing language to Malay so that not every word gets a red underline.'],
        ['nama' => 'Word Count', 'cat' => 'semakan', 'desc' => 'Counts of words, characters, and paragraphs.', 'langkah' => 'Review → Word Count, or click the word count in the status bar.', 'pintasan' => 'Ctrl+Shift+G'],

        // === MAIL MERGE ===
        ['nama' => 'Mail Merge: Letters', 'cat' => 'mel', 'popular' => true, 'desc' => 'One letter for many recipients, with names and addresses taken from an Excel list.', 'langkah' => 'Mailings → Start Mail Merge → Letters → Select Recipients → Use an Existing List (the Excel file) → Insert Merge Field → Preview Results → Finish & Merge.', 'tip' => 'The first row in Excel must hold the column headings (Name, Address, and so on). Leave no blank rows.'],
        ['nama' => 'Mail Merge: Labels', 'cat' => 'mel', 'desc' => 'Print address labels or name tags from a list.', 'langkah' => 'Mailings → Start Mail Merge → Labels → choose the label vendor and size → Select Recipients → Update Labels → Finish & Merge.'],

        // === SAVE AND SHARE ===
        ['nama' => 'Save as PDF', 'cat' => 'fail', 'popular' => true, 'desc' => 'A final document whose layout does not change on another computer.', 'langkah' => 'File → Save As → choose PDF, or File → Export → Create PDF/XPS.', 'pintasan' => 'F12 (Save As)'],
        ['nama' => 'AutoSave & Version History', 'cat' => 'fail', 'desc' => 'Save automatically and restore earlier versions.', 'langkah' => 'Save the file to OneDrive → the AutoSave switch at the top left. Earlier versions: File → Info → Version History.'],
        ['nama' => 'Inspect Document', 'cat' => 'fail', 'desc' => 'Remove comments, tracked changes, and hidden personal information before you send a document.', 'langkah' => 'File → Info → Check for Issues → Inspect Document.'],
        ['nama' => 'Restrict Editing', 'cat' => 'fail', 'desc' => 'Limit editing, for example a form that can only be filled in.', 'langkah' => 'Review → Restrict Editing.'],
    ],
];
