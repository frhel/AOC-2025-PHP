<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2025/day/3
// Solution by: https://github.com/frhel (Fry)
// Part1:
// Part2:
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2025php\Solutions;

use frhel\adventofcode2025php\Tools\Prenta;
use frhel\adventofcode2025php\Tools\Utils;
use Psr\Log\LogLevel;

class Day3 extends Day
{
    function __construct(private int $day, $bench = 0, $ex = 0) {
        parent::__construct($day, $bench, $ex);
    }


    /**
     * Solves the problem. Needs to be public so we can call it from the benchmarking code
     *
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data, $part1 = 0, $part2 = 0) {
        $data = $this->parse_input($this->load_data($this->day, $this->ex)); $this->data = $data;

        $part1 = $this->solve_part1($data);
        $part2 = $this->solve_part2($data);

        return [$part1, $part2];
    }

    private function solve_part1($data) {
        $total_joltage = 0;
        foreach ($data as $row) {
            $max = -1;
            for ($i = 0; $i < count($row) - 1; $i++) {
                for ($j = $i+1; $j < count($row); $j++) {
                    $max = max($max, (int)($row[$i] . $row[$j]));
                }
            }
            $total_joltage += $max;
        }
        return $total_joltage;
    }

    private function solve_part2($data) {
        $total_joltage = 0;
        foreach ($data as $row) {
            
            $row_len = count($row);
            // Building up the answer cell by cell, appending from right to left
            // Using a memoization table to store all max values for each cell and number of cells used
            $n_cells = 12; // Max 12 cells
            // Initialize all memoization table cells to -1
            // The table has row_len rows and n_cells columns
            $memo = array_fill(0, $row_len, array_fill(0, $n_cells + 1, -1));

            // Loop through each cell
            for ($i = 0; $i < $row_len; $i++) {
                // Set max value cell for the current last cell
                $memo[$i][1] = max($memo[$i][1], (int)$row[$i]);
                
                // For every starting cell, we loop through all the following cells
                for ($l = $i + 1; $l < $row_len; $l++) {
                    // For every following cell, we try to append it to the previous cells' max values
                    for ($m = 1; $m < $n_cells; $m++) {

                        // For every previous cell combination, we try to append the current cell
                        // Increment the cell count by appending the current cell to the previous cell combination
                        $num = (int)$memo[$i][$m] . $row[$l];
                        $memo[$l][$m+1] = max($memo[$l][$m+1], $num);

                    }
                }
            }
            // Grab the maximum value by checking the last column of the memoization table
            // for all rows (i.e. all ending cells)
            $max = -1;
            for ($i = 0; $i < $row_len; $i++) {
                $max = max($max, $memo[$i][$n_cells]);
            }
            $total_joltage += $max;
        }
        return $total_joltage;
    }

    /**
     * Parses the input data into a usable format
     *
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {
        $data = preg_split('/\r\n|\r|\n/', $data);
        
        $data = array_map(function($line) {
            return str_split($line);
        }, $data);

        return $data;
    }
}
