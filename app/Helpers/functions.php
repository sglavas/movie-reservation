<?php

use function Laravel\Prompts\error;

/**
 * Parses a .csv file and turns it into an array.
 * 
 * @param string $fileName Path to the resource file.
 * @return array Returns a multidimensional array, or null on failure.
 */
function csvToArray($fileName) {
    // Check if file exists
    if(!file_exists($fileName)){
        error("Data could not be retrieved.");
        return;
    }

    // Map over the .csv file and extract the rows
    $rows = array_map('str_getcsv', file($fileName));

    $columnNames = array_shift($rows);

    $data = [];

    // Combine into an associative array
    foreach($rows as $row) {
        $data[] = array_combine($columnNames, $row);
    }

    return $data;

}