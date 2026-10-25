<?php

declare(strict_types=1);

// Teks EN rujukan fungsi (info); kunci = name dalam config/formulas/info.php, susunan sama.
return [
    'CELL' => ['desc' => 'Returns information about a cell\'s formatting, location, or contents, depending on the info type requested.'],
    'ERROR.TYPE' => ['desc' => 'Returns a number that identifies the error type (1 = <code>#NULL!</code>, 2 = <code>#DIV/0!</code>, and so on), or <code>#N/A</code> if there is no error.'],
    'INFO' => ['desc' => 'Returns information about the current operating environment, such as the operating system or the number of active worksheets.'],
    'ISBLANK' => ['desc' => 'Returns TRUE if a cell is empty.', 'usecase' => 'Check whether the required fields in a registration form have been filled in.'],
    'ISERR' => ['desc' => 'Returns TRUE if a value is any error except <code>#N/A</code>.'],
    'ISERROR' => ['desc' => 'Returns TRUE if a value is any type of error, including <code>#N/A</code>.', 'usecase' => 'Detect failing formulas so you can show a clear message instead of an error.'],
    'ISEVEN' => ['desc' => 'Returns TRUE if a number is even.', 'sample' => 'TRUE'],
    'ISFORMULA' => ['desc' => 'Returns TRUE if the referenced cell contains a formula.'],
    'ISLOGICAL' => ['desc' => 'Returns TRUE if a value is a logical value (TRUE or FALSE).'],
    'ISNA' => ['desc' => 'Returns TRUE if a value is the <code>#N/A</code> error.'],
    'ISNONTEXT' => ['desc' => 'Returns TRUE if a value is not text, including an empty cell.'],
    'ISNUMBER' => ['desc' => 'Returns TRUE if a value is a number.', 'usecase' => 'Make sure a price column contains only numbers before you total it.'],
    'ISODD' => ['desc' => 'Returns TRUE if a number is odd.', 'sample' => 'TRUE'],
    'ISOMITTED' => ['desc' => 'Returns TRUE if an argument in a <code>LAMBDA</code> function was omitted.'],
    'ISREF' => ['desc' => 'Returns TRUE if a value is a cell or range reference.'],
    'ISTEXT' => ['desc' => 'Returns TRUE if a value is text.'],
    'N' => ['desc' => 'Converts a value to a number: numbers stay as they are, dates become serial numbers, TRUE becomes 1, errors are returned unchanged, and anything else becomes 0.'],
    'NA' => ['desc' => 'Returns the <code>#N/A</code> error on purpose, for example to mark data that is not yet available.'],
    'SHEET' => ['desc' => 'Returns the sheet number of the referenced sheet, or of the current sheet if no argument is given.'],
    'SHEETS' => ['desc' => 'Returns the number of sheets in a reference, or in the whole workbook if no argument is given.'],
    'TYPE' => ['desc' => 'Returns a code for the type of a value: 1 number, 2 text, 4 logical value, 16 error, 64 array, 128 compound data.'],
];
