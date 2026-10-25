<?php

declare(strict_types=1);

/**
 * PowerPoint cheat sheet (/en/powerpoint/cheatsheet), EN. Same structure as ../cheatsheet.php (schema: docs/REVAMP.md).
 * Shortcuts = PowerPoint for Microsoft 365 on Windows.
 */
return [
    'lencana' => 'PowerPoint cheat sheet',
    'tajuk' => 'PowerPoint cheat sheet',
    'intro' => 'Keyboard shortcuts, Slide Master, arranging objects, and presenting controls on one page.',
    'bahagian' => [
        [
            'id' => 'shortcut', 'ikon' => 'keyboard', 'tajuk' => 'Essential shortcuts', 'jenis' => 'pintasan',
            'sub' => 'PowerPoint for Windows. On a Mac, most Ctrl shortcuts use Command instead, but not all of them.',
            'item' => [
                ['keys' => ['Ctrl', 'M'], 'name' => 'New Slide', 'desc' => 'Insert a new slide after the current one.'],
                ['keys' => ['Ctrl', 'D'], 'name' => 'Duplicate', 'desc' => 'Duplicate the selected object or slide.'],
                ['keys' => ['Ctrl', 'G'], 'name' => 'Group', 'desc' => 'Combine the selected objects into one group.'],
                ['keys' => ['Ctrl', 'Shift', 'G'], 'name' => 'Ungroup', 'desc' => 'Break a group back into its separate objects.'],
                ['keys' => ['Ctrl', 'Shift', 'C'], 'name' => 'Copy Formatting', 'desc' => 'Copy the formatting of an object or text. Paste it with Ctrl+Shift+V.'],
                ['keys' => ['Ctrl', 'K'], 'name' => 'Insert Link', 'desc' => 'Link to a web page or to another slide in the same file.'],
                ['keys' => ['Ctrl', 'E'], 'name' => 'Center', 'desc' => 'Center the selected paragraph.'],
                ['keys' => ['Ctrl', 'Shift', '>'], 'name' => 'Increase Font Size', 'desc' => 'Make the selected text larger.'],
                ['keys' => ['Alt', 'F10'], 'name' => 'Selection Pane', 'desc' => 'List every object on the slide so you can select, hide, and reorder layers.'],
                ['keys' => ['Shift', 'Drag'], 'name' => 'Constrain', 'desc' => 'Move an object in a straight horizontal or vertical line, or resize it proportionally.'],
                ['keys' => ['Ctrl', 'Drag'], 'name' => 'Drag to copy', 'desc' => 'Hold Ctrl while dragging an object to create a copy.'],
                ['keys' => ['Ctrl', '←↑→↓'], 'name' => 'Nudge', 'desc' => 'Move the selected object in very small steps.'],
            ],
        ],
        [
            'id' => 'presenting', 'ikon' => 'presentation', 'tajuk' => 'While presenting', 'jenis' => 'pintasan',
            'sub' => 'Keys that work during a slide show, so you never have to reach for the mouse in front of an audience.',
            'item' => [
                ['keys' => ['F5'], 'name' => 'From Beginning', 'desc' => 'Start the slide show from the first slide.'],
                ['keys' => ['Shift', 'F5'], 'name' => 'From Current Slide', 'desc' => 'Start the slide show from the slide that is open.'],
                ['keys' => ['Alt', 'F5'], 'name' => 'Presenter View', 'desc' => 'Start the slide show with your notes and the next slide on your screen.'],
                ['keys' => ['B'], 'name' => 'Black screen', 'desc' => 'Press again to return. W shows a white screen.'],
                ['keys' => ['5', 'Enter'], 'name' => 'Go to slide', 'desc' => 'Type the slide number, then press Enter.'],
                ['keys' => ['Ctrl', 'L'], 'name' => 'Laser Pointer', 'desc' => 'Turn the mouse pointer into a laser pointer.'],
                ['keys' => ['Ctrl', 'P'], 'name' => 'Pen', 'desc' => 'Write or draw on the slide during the show.'],
                ['keys' => ['Esc'], 'name' => 'End Show', 'desc' => 'Exit the slide show.'],
            ],
        ],
        [
            'id' => 'master', 'ikon' => 'layout-template', 'tajuk' => 'Slide Master for consistent slides', 'jenis' => 'kad',
            'sub' => 'One change on the Slide Master updates every slide that uses it. Editing slides one at a time leads to inconsistent slides.',
            'item' => [
                ['Open Slide Master', '<strong>View → Slide Master</strong>. The top thumbnail is the master for all layouts. The layouts below it represent specific slide types.'],
                ['Logo on every slide', 'Place the logo on the top master. It appears on all slides and cannot be moved by accident while you edit regular slides.'],
                ['Theme fonts and colors', '<strong>Slide Master → Colors / Fonts</strong>. Every slide follows them, including charts and SmartArt.'],
                ['Custom layouts', '<strong>Slide Master → Insert Layout</strong>, then <strong>Insert Placeholder</strong>. Useful for slide types you repeat, such as a monthly financial summary.'],
            ],
        ],
        [
            'id' => 'arrange', 'ikon' => 'align-left', 'tajuk' => 'Arranging objects', 'jenis' => 'jadual',
            'sub' => 'A clean slide has aligned objects and even spacing.',
            'kepala' => ['Task', 'Path', 'Notes'],
            'baris' => [
                ['Align objects', 'Shape Format → Align → Align Middle / Align Center', 'Select two or more objects first. For pictures, the tab is Picture Format.'],
                ['Even spacing', 'Shape Format → Align → Distribute Horizontally', 'Makes the gaps between the selected objects equal. Most useful with three or more objects.'],
                ['Guides', 'View → Guides / Gridlines', 'Smart Guides appear on their own when an object lines up with another object.'],
                ['Select a hidden object', 'Home → Arrange → Selection Pane', 'Shortcut Alt+F10. You can also rename and hide objects here.'],
            ],
        ],
        [
            'id' => 'mistakes', 'ikon' => 'triangle-alert', 'tajuk' => 'Common mistakes', 'jenis' => 'kesilapan',
            'sub' => 'The most frequent problems in work presentations, and how to fix them.',
            'item' => [
                ['A slide full of text', 'The audience reads the slide and stops listening. <strong>Fix:</strong> one idea per slide; put the details in the speaker notes.'],
                ['Formatting each slide by hand', 'Slides end up inconsistent and hard to change. <strong>Fix:</strong> change the formatting in <strong>View → Slide Master</strong>.'],
                ['Animating every object', 'It slows the talk and distracts the audience. <strong>Fix:</strong> animate only to show a sequence; use Morph for transitions between slides.'],
                ['Pasting an Excel chart as a picture', 'The numbers go out of date when the data changes. <strong>Fix:</strong> <strong>Paste → Use Destination Theme &amp; Link Data</strong>.'],
                ['Sending a .pptx with custom fonts', 'The recipient\'s computer substitutes the fonts and the layout shifts. <strong>Fix:</strong> <strong>File → Options → Save → Embed fonts in the file</strong>, or export to PDF.'],
            ],
            'amalan' => [
                'Use high contrast: dark text on a light background, or the reverse. Projectors wash out color.',
                'Use at least 24 pt for body text and 36 pt for titles.',
                'Practice with <strong>Slide Show → Rehearse Timings</strong> to estimate your time.',
                'Run <strong>Review → Check Accessibility</strong> and add Alt Text to every picture.',
            ],
        ],
    ],
];
