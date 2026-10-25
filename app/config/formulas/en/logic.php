<?php

declare(strict_types=1);

// Teks EN rujukan fungsi (logic); kunci = name dalam config/formulas/logic.php, susunan sama.
return [
    'AND' => ['desc' => 'Returns <code>TRUE</code> if all arguments are <code>TRUE</code>, and <code>FALSE</code> if any argument is <code>FALSE</code>.', 'usecase' => 'Checking that a record meets several conditions at once, such as sales above target and complaints below a limit.'],
    'BYCOL' => ['desc' => 'Applies a <code>LAMBDA</code> to each column of an array and returns one result per column.'],
    'BYROW' => ['desc' => 'Applies a <code>LAMBDA</code> to each row of an array and returns one result per row.'],
    'FALSE' => ['desc' => 'Returns the logical value <code>FALSE</code>.'],
    'IF' => ['desc' => 'Tests a condition and returns one value if it is true and another value if it is false.', 'usecase' => 'Marking each student as Pass or Fail based on a minimum score.'],
    'IFERROR' => ['desc' => 'Returns a value you specify if a formula produces an error, and the result of the formula otherwise.', 'usecase' => 'Showing 0 or a blank on a dashboard instead of <code>#DIV/0!</code> or <code>#N/A</code>.', 'sample' => '0 if A2/B2 returns an error such as #DIV/0!'],
    'IFNA' => ['desc' => 'Returns a value you specify only when a formula results in the <code>#N/A</code> error, and the result of the formula otherwise.'],
    'IFS' => ['desc' => 'Checks several conditions in order and returns the value for the first one that is true, without nesting <code>IF</code> functions.', 'usecase' => 'Converting exam scores into A, B, or C grades.'],
    'LAMBDA' => ['desc' => 'Creates a reusable custom function with its own parameters and calculation, without VBA.'],
    'LET' => ['desc' => 'Assigns names to values or intermediate calculations inside a formula so they can be reused and the formula is easier to read.', 'usecase' => 'Shortening a long formula that repeats the same lookup several times.'],
    'MAKEARRAY' => ['desc' => 'Returns an array with the number of rows and columns you specify, with each value calculated by a <code>LAMBDA</code>.'],
    'MAP' => ['desc' => 'Applies a <code>LAMBDA</code> to each value in one or more arrays and returns an array of results of the same size.'],
    'NOT' => ['desc' => 'Reverses the logical value of its argument, turning <code>TRUE</code> into <code>FALSE</code> and <code>FALSE</code> into <code>TRUE</code>.'],
    'OR' => ['desc' => 'Returns <code>TRUE</code> if at least one argument is <code>TRUE</code>, and <code>FALSE</code> if all arguments are <code>FALSE</code>.'],
    'REDUCE' => ['desc' => 'Reduces an array to a single accumulated value by applying a <code>LAMBDA</code> to each value together with an accumulator.'],
    'SCAN' => ['desc' => 'Applies a <code>LAMBDA</code> to each value in an array and returns an array of every intermediate accumulator value, such as a running total.', 'sample' => 'Running total of A1:A5'],
    'SWITCH' => ['desc' => 'Compares an expression against a list of values and returns the result for the first match, or a default value if nothing matches.'],
    'TRUE' => ['desc' => 'Returns the logical value <code>TRUE</code>.'],
    'XOR' => ['desc' => 'Returns <code>TRUE</code> when an odd number of arguments are <code>TRUE</code>, and <code>FALSE</code> when an even number are.', 'sample' => 'TRUE'],
];
