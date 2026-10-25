<?php

declare(strict_types=1);

/**
 * PowerPoint feature reference (/en/powerpoint/reference), EN. Same nama, cat, and pintasan as ../rujukan.php.
 * Ribbon paths = PowerPoint for Microsoft 365 (Windows).
 */
return [
    'tajuk' => 'PowerPoint feature reference',
    'intro' => 'Key Microsoft PowerPoint features with exact ribbon paths, keyboard shortcuts, and notes for work presentations.',
    'kategori' => [
        'slaid' => ['Slides', 'presentation'],
        'master' => ['Slide Master and themes', 'layout-template'],
        'objek' => ['Objects and arranging', 'layers'],
        'media' => ['Pictures, charts, and media', 'image'],
        'gerak' => ['Animations and transitions', 'play'],
        'bentang' => ['Presenting', 'monitor'],
        'fail' => ['Save and share', 'share-2'],
    ],
    'ciri' => [
        // === SLIDES ===
        ['nama' => 'New Slide & Layout', 'cat' => 'slaid', 'popular' => true, 'desc' => 'Insert a new slide with a specific layout, such as Title and Content, Two Content, or Comparison.', 'langkah' => 'Home → New Slide (arrow) → choose a layout. To change the layout of an existing slide: Home → Layout.', 'pintasan' => 'Ctrl+M'],
        ['nama' => 'Duplicate Slide', 'cat' => 'slaid', 'desc' => 'Copy a slide to build the next version. This is also the basis of the Morph transition.', 'langkah' => 'Right-click the slide thumbnail → Duplicate Slide.', 'pintasan' => 'Ctrl+D'],
        ['nama' => 'Sections', 'cat' => 'slaid', 'desc' => 'Group slides into sections so they are easier to organize and move together.', 'langkah' => 'Click between two slides in the thumbnail pane → Home → Section → Add Section.'],
        ['nama' => 'Reuse Slides', 'cat' => 'slaid', 'desc' => 'Insert slides from another file without opening that file.', 'langkah' => 'Home → New Slide (arrow) → Reuse Slides.'],
        ['nama' => 'Hide Slide', 'cat' => 'slaid', 'desc' => 'Keep backup slides in the file without showing them during the presentation.', 'langkah' => 'Right-click the slide thumbnail → Hide Slide, or Slide Show → Hide Slide.'],
        ['nama' => 'Slide Number & Footer', 'cat' => 'slaid', 'desc' => 'Show the slide number and footer text on every slide.', 'langkah' => 'Insert → Header & Footer → select Slide number and Footer → Apply to All.'],

        // === SLIDE MASTER AND THEMES ===
        ['nama' => 'Slide Master', 'cat' => 'master', 'popular' => true, 'desc' => 'The parent template that sets the logo, fonts, colors, and title position for every slide.', 'langkah' => 'View → Slide Master. To exit: Slide Master → Close Master View.', 'tip' => 'A logo on the top master appears on every slide and stays put while you edit regular slides.'],
        ['nama' => 'Theme Colors & Fonts', 'cat' => 'master', 'desc' => 'A set of brand colors and fonts used by charts, SmartArt, and shapes.', 'langkah' => 'Design → Variants (More arrow) → Colors or Fonts → Customize Colors / Customize Fonts.'],
        ['nama' => 'Designer', 'cat' => 'master', 'desc' => 'Automatic layout suggestions for the current slide. Requires Microsoft 365 and an internet connection.', 'langkah' => 'Design → Designer.'],
        ['nama' => 'Slide Size', 'cat' => 'master', 'desc' => 'Choose 16:9 for screens, 4:3, or a custom size such as A4 paper.', 'langkah' => 'Design → Slide Size → Widescreen, Standard, or Custom Slide Size.', 'tip' => 'Set the size before you start. Changing it later shifts or rescales every object.'],

        // === OBJECTS AND ARRANGING ===
        ['nama' => 'Align & Distribute', 'cat' => 'objek', 'popular' => true, 'desc' => 'Line objects up and make the spacing between them equal.', 'langkah' => 'Select the objects → Shape Format → Align → Align Middle or Distribute Horizontally.'],
        ['nama' => 'Group', 'cat' => 'objek', 'desc' => 'Combine several objects so they move and resize together.', 'langkah' => 'Select the objects → Shape Format → Group → Group.', 'pintasan' => 'Ctrl+G / Ctrl+Shift+G (Ungroup)'],
        ['nama' => 'Selection Pane', 'cat' => 'objek', 'popular' => true, 'desc' => 'A list of every object on the slide. Use it to select buried objects, rename, hide, and reorder layers.', 'langkah' => 'Home → Arrange → Selection Pane.', 'pintasan' => 'Alt+F10', 'tip' => 'Name objects before you use Morph. Objects with the same name on two slides are matched exactly.'],
        ['nama' => 'Format Painter', 'cat' => 'objek', 'desc' => 'Copy the formatting of one object or piece of text to another.', 'langkah' => 'Select the source object → Home → Format Painter → click the target object. Double-click Format Painter to apply it repeatedly.', 'pintasan' => 'Ctrl+Shift+C / Ctrl+Shift+V'],
        ['nama' => 'Guides & Gridlines', 'cat' => 'objek', 'desc' => 'Lines that keep margins and columns consistent across slides.', 'langkah' => 'View → select Guides or Gridlines. To add a guide: right-click a guide → Add Vertical Guide / Add Horizontal Guide.'],
        ['nama' => 'Convert to SmartArt', 'cat' => 'objek', 'desc' => 'Turn a bulleted list into a process, cycle, or hierarchy diagram.', 'langkah' => 'Select the text box → Home → Convert to SmartArt.'],

        // === PICTURES, CHARTS, AND MEDIA ===
        ['nama' => 'Crop to Shape', 'cat' => 'media', 'desc' => 'Crop a picture into a shape, such as a circle for a profile photo.', 'langkah' => 'Click the picture → Picture Format → Crop (arrow) → Crop to Shape.'],
        ['nama' => 'Linked Excel Chart', 'cat' => 'media', 'popular' => true, 'desc' => 'A chart that follows changes to the data in the original Excel file.', 'langkah' => 'Copy the chart in Excel → in PowerPoint: Home → Paste (arrow) → Use Destination Theme & Link Data. To update: Chart Design → Refresh Data.'],
        ['nama' => 'Icons', 'cat' => 'media', 'desc' => 'Vector icons that you can recolor to match the theme.', 'langkah' => 'Insert → Icons.'],
        ['nama' => 'Compress Pictures', 'cat' => 'media', 'desc' => 'Reduce the size of a file that contains many pictures.', 'langkah' => 'Click a picture → Picture Format → Compress Pictures.'],
        ['nama' => 'Alt Text', 'cat' => 'media', 'desc' => 'A description of a picture for people who use screen readers.', 'langkah' => 'Right-click the picture → View Alt Text. To check every slide: Review → Check Accessibility.'],

        // === ANIMATIONS AND TRANSITIONS ===
        ['nama' => 'Transition', 'cat' => 'gerak', 'desc' => 'The effect shown when you move from one slide to the next.', 'langkah' => 'Transitions → choose an effect → Apply To All if you want every slide to match.'],
        ['nama' => 'Morph', 'cat' => 'gerak', 'popular' => true, 'desc' => 'Objects move smoothly from their position on one slide to a new position on the next slide.', 'langkah' => 'Duplicate the slide → change the position or size of objects on the copy → select the second slide → Transitions → Morph.'],
        ['nama' => 'Animation Pane', 'cat' => 'gerak', 'desc' => 'The order and timing of every animation within a slide.', 'langkah' => 'Select an object → Animations → choose an effect. To set the order: Animations → Animation Pane.', 'tip' => 'Use animation to show the order of ideas, not as decoration on every object.'],

        // === PRESENTING ===
        ['nama' => 'Presenter View', 'cat' => 'bentang', 'popular' => true, 'desc' => 'Your notes, the next slide, and a timer appear on your screen. The audience sees only the slide.', 'langkah' => 'Slide Show → select Use Presenter View → From Beginning.', 'pintasan' => 'Alt+F5'],
        ['nama' => 'Speaker Notes', 'cat' => 'bentang', 'desc' => 'Notes below each slide. They are not shown to the audience.', 'langkah' => 'Click Notes on the status bar at the bottom, or View → Notes Page.'],
        ['nama' => 'Rehearse Timings', 'cat' => 'bentang', 'desc' => 'Practice and record how long you spend on each slide.', 'langkah' => 'Slide Show → Rehearse Timings.'],
        ['nama' => 'Slide Show Controls', 'cat' => 'bentang', 'desc' => 'Keys to start the show, jump between slides, blank the screen, and use the laser pointer, pen, or eraser.', 'langkah' => 'F5 start from the beginning · Shift+F5 start from the current slide · slide number + Enter to jump · B black screen · W white screen · Esc end. Ctrl+L laser, Ctrl+P pen, Ctrl+E eraser.', 'pintasan' => 'Ctrl+L · Ctrl+P · Ctrl+E'],

        // === SAVE AND SHARE ===
        ['nama' => 'Export PDF', 'cat' => 'fail', 'popular' => true, 'desc' => 'Share slides in a format whose layout does not change.', 'langkah' => 'File → Export → Create PDF/XPS Document → Create PDF/XPS. For handouts: Options → Publish what → Handouts.'],
        ['nama' => 'Export Video', 'cat' => 'fail', 'desc' => 'Turn the presentation, including recorded timings and narration, into an MP4 video.', 'langkah' => 'File → Export → Create a Video.'],
        ['nama' => 'Embed Fonts', 'cat' => 'fail', 'desc' => 'Carry custom fonts with the file so they are not substituted on other computers.', 'langkah' => 'File → Options → Save → select Embed fonts in the file.'],
    ],
];
