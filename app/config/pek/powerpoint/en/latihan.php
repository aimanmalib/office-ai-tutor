<?php

declare(strict_types=1);

/**
 * PowerPoint practice, EN. Same id, mode, level, answers, correct_index, and option order as ../latihan.php.
 */
['pintasan' => $p, 'pilihan' => $c] = require __DIR__ . '/../../_soalan.php';

return [
    'pintasan' => [
        'name' => 'Essential shortcuts',
        'icon' => 'keyboard',
        'tagline' => 'Shortcuts for building and presenting slides',
        'description' => 'Press the correct key combination. On a phone, type it with +, for example Ctrl+letter. Covers slides, objects, and Slide Show.',
        'color' => 'purple',
        'questions' => [
            $p('p1', 'Slides', '🗂️', 1, 'Press the shortcut to <strong>insert a new slide</strong>.', ['Ctrl+M'],
                'It does the same thing as the New Slide button.', '<strong>Ctrl+M</strong> inserts a new slide after the current one (Home → New Slide).'),
            $p('p2', 'Objects', '📐', 1, 'Press the shortcut to <strong>group</strong> the selected objects.', ['Ctrl+G'],
                'G for Group.', '<strong>Ctrl+G</strong> is Group. <strong>Ctrl+Shift+G</strong> is Ungroup.'),
            $p('p3', 'Objects', '📐', 2, 'Press the shortcut to <strong>ungroup</strong> a group of objects.', ['Ctrl+Shift+G'],
                'The Group shortcut plus one more modifier key.', '<strong>Ctrl+Shift+G</strong> breaks the group apart so you can change each object separately.'),
            $p('p4', 'Objects', '📋', 2, 'Press the shortcut to <strong>open the Selection Pane</strong> and select a buried object.', ['Alt+F10'],
                'Alt plus a function key.', '<strong>Alt+F10</strong> opens the Selection Pane (Home → Arrange → Selection Pane).'),
            $p('p5', 'Presenting', '🎤', 1, 'Press the shortcut to <strong>start the slide show from the first slide</strong>.', ['F5'],
                'A single function key.', '<strong>F5</strong> starts from the beginning. <strong>Shift+F5</strong> starts from the current slide.'),
            $p('p6', 'Presenting', '🎤', 2, 'Press the shortcut to <strong>start from the slide that is open</strong>, not from the beginning.', ['Shift+F5'],
                'The start-from-beginning key plus one modifier.', '<strong>Shift+F5</strong> is useful when you resume a presentation after a Q&amp;A session.'),
            $p('p7', 'Presenting', '🖥️', 2, 'Press the shortcut to <strong>start in Presenter View</strong>, with notes on your screen only.', ['Alt+F5'],
                'Not Shift this time.', '<strong>Alt+F5</strong> starts the slide show in Presenter View.'),
            $p('p8', 'Presenting', '⬛', 2, 'During a presentation, you want the audience to focus on you. Press the key for a <strong>black screen</strong>.', ['B', '.'],
                'The first letter of "Black".', '<strong>B</strong> (or period) shows a black screen. Press it again to return. <strong>W</strong> shows a white screen.'),
            $p('p9', 'Presenting', '🔴', 3, 'During a presentation, press the shortcut for the <strong>laser pointer</strong>.', ['Ctrl+L'],
                'L for Laser.', '<strong>Ctrl+L</strong> turns the mouse pointer into a laser. <strong>Ctrl+P</strong> is the pen and <strong>Ctrl+E</strong> is the eraser.'),
            $p('p10', 'Links', '🔗', 1, 'Press the shortcut to <strong>insert a link</strong> to another slide or a website.', ['Ctrl+K'],
                'The same in Excel, Word, and Outlook.', '<strong>Ctrl+K</strong> opens Insert Hyperlink, including links to another slide in the same file (Place in This Document).'),
        ],
    ],
    'slaid' => [
        'name' => 'Clean slides',
        'icon' => 'layout-template',
        'tagline' => 'Slide Master, arranging objects, charts, and presenting',
        'description' => 'Choose the right approach for work presentations that are consistent, tidy, and easy to update.',
        'color' => 'amber',
        'questions' => [
            $c('s1', 'Slide Master', '🎨', 1, 'The company logo must appear on <strong>every slide</strong>. What is the most suitable approach?',
                ['Copy and paste the logo onto each slide', 'Place the logo on the Slide Master (View → Slide Master)', 'Make the logo the background picture of the first slide', 'Insert → Header & Footer'], 1,
                'One place for all slides.', 'A logo on the <strong>Slide Master</strong> appears on every slide and stays put while you edit. If you paste it onto each slide, 30 slides means 30 logos to update.'),
            $c('s2', 'Slide Master', '🎨', 2, 'Your manager asks you to change the font of every title in 40 slides. What is the fastest way?',
                ['Select each title one at a time', 'Change the title font in the Slide Master (or Design → Variants → Fonts)', 'Find and Replace', 'Format Painter on each slide'], 1,
                'Change it at the source.', 'A title font set in the <strong>Slide Master</strong>, or the theme font, changes at once on every slide that uses that layout.'),
            $c('s3', 'Arranging', '📐', 1, 'Three boxes need <strong>equal spacing</strong> between them.',
                ['Drag them and judge by eye', 'Align → Distribute Horizontally', 'Group, then Center', 'Resize the boxes'], 1,
                'The feature is in the Align menu.', '<strong>Distribute Horizontally</strong> makes the gaps between the selected objects equal.'),
            $c('s4', 'Arranging', '📋', 2, 'A shape is buried under a picture and cannot be clicked. How do you select it?',
                ['Delete the picture first', 'Selection Pane (Alt+F10) → click the shape\'s name', 'Ctrl+A', 'Undo repeatedly'], 1,
                'There is a list that shows every object.', 'The <strong>Selection Pane</strong> lists every object on the slide. You can select, hide, or reorder their layers.'),
            $c('s5', 'Charts', '📊', 2, 'A sales chart from Excel must <strong>stay current</strong> when the Excel data changes.',
                ['Take a screenshot of the chart', 'Paste → Use Destination Theme & Link Data', 'Paste → Picture', 'Retype the numbers'], 1,
                'The paste option that contains the word "Link".', '<strong>Link Data</strong> links the chart to the Excel file. Update it with Chart Design → Refresh Data.'),
            $c('s6', 'Animation', '✨', 2, 'What is the difference between a <strong>Transition</strong> and an <strong>Animation</strong>?',
                ['They are the same', 'Transition = between slides; Animation = objects within a slide', 'Transition = objects; Animation = slides', 'Transitions are only for video'], 1,
                'They are on different tabs.', 'A <strong>Transition</strong> happens when you change slides. An <strong>Animation</strong> moves objects within one slide.'),
            $c('s7', 'Animation', '✨', 3, 'You want a box to move smoothly from the left of slide 3 to the right of slide 4. Which feature is the most suitable?',
                ['Animation → Fly In', 'Duplicate the slide, move the box, then Transitions → Morph', 'A Motion Path on each box', 'Hyperlink'], 1,
                'A type of transition in Microsoft 365.', '<strong>Morph</strong> animates the differences between two nearly identical slides. You do not need to draw a motion path.'),
            $c('s8', 'Presenting', '🖥️', 1, 'You want to see your <strong>speaker notes and the next slide</strong> while the audience sees only the slide.',
                ['Reading View', 'Presenter View', 'Slide Sorter', 'Outline View'], 1,
                'Slide Show → Use …', '<strong>Presenter View</strong> (Alt+F5) shows your notes, a timer, and the next slide on your screen only.'),
            $c('s9', 'Design', '🎨', 1, 'Your slide is full of long paragraphs. What is the most suitable advice?',
                ['Shrink the font so it fits', 'One idea per slide; put the details in Speaker Notes', 'Add an animation to every line', 'Use two columns of text'], 1,
                'The audience should listen, not read.', 'Keep the text on the slide short. Put the details in the <strong>Speaker Notes</strong> for your own reference.'),
            $c('s10', 'Sharing', '💾', 2, 'A .pptx file uses a custom brand font and will be opened on a client\'s computer. What is the correct step?',
                ['No step is needed because fonts travel with the file', 'File → Options → Save → Embed fonts in the file (or send a PDF)', 'Change the Slide Size to 4:3', 'Compress Pictures'], 1,
                'The font needs to travel with the file.', 'Without <strong>Embed fonts</strong>, the recipient\'s computer substitutes the font and the layout can shift. A PDF locks the appearance of the slides.'),
        ],
    ],
];
