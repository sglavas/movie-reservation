<?php

namespace App\Services\Seeder;

use RuntimeException;


class ExtractSeedService
{
    /**
     * Parses a .csv file and turns it into an array.
     *
     * @param string $fileName Path to the resource file.
     * @return array Returns a multidimensional array, or throws a RuntimeException on failure.
     */
    public function extractData(string $fileName): array
    {
        if (!file_exists($fileName) || !is_readable($fileName)) {
            throw new RuntimeException("CSV Source file not found or unreadable: {$fileName}");
        }

        // Map over the .csv file and extract the rows
        $rows = array_map('str_getcsv', file($fileName));

        $columnNames = array_shift($rows);

        $data = array_map(function ($row) use($columnNames){
            return array_combine($columnNames, $row);
        }, $rows);

        return $data;
    }
}
