<?php

declare(strict_types=1);

// Teks EN rujukan fungsi (cube); kunci = name dalam config/formulas/cube.php, susunan sama.
return [
    'CUBEKPIMEMBER' => ['desc' => 'Returns a KPI property, such as value, goal, or status, from a cube and displays the KPI name in the cell.'],
    'CUBEMEMBER' => ['desc' => 'Returns a member or tuple from a cube hierarchy, if it exists.'],
    'CUBEMEMBERPROPERTY' => ['desc' => 'Returns the value of a property for a member in a cube.'],
    'CUBERANKEDMEMBER' => ['desc' => 'Returns the nth member of a cube set by rank, such as the top-selling product.'],
    'CUBESET' => ['desc' => 'Defines a set of members or tuples by sending a set expression to the cube, for use by other cube functions.'],
    'CUBESETCOUNT' => ['desc' => 'Returns the number of items in a set defined by <code>CUBESET</code>.'],
    'CUBEVALUE' => ['desc' => 'Returns an aggregated value from a cube for the specified combination of members.'],
];
