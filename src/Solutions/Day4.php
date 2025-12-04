<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2025/day/4
// Solution by: https://github.com/frhel (Fry)
// Part1:
// Part2:
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2025php\Solutions;

use frhel\adventofcode2025php\Tools\Prenta;
use frhel\adventofcode2025php\Tools\Utils;

class Day4 extends Day
{
    // Save a matrix of directions to check around each roll
    // for easier looping. These are the 8 adjacent directions.
    // They make it possible to avoid writing out each direction
    // check manually multiple times.
    // Usage: foreach ($dirs as $dir) { $ax = $x + $dir[0]; $ay = $y + $dir[1]; ... }
    private array $DIRS = [
        [0, 1],
        [0, -1],
        [1, 1],
        [-1, 1],
        [1, -1],
        [-1, -1],
        [1, 0],
        [-1, 0],
    ];

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

        $part1 = $this->solve_part1($data, $this->DIRS);

        // Add part 1's answer to part 2 as well since part 2 builds on part 1
        $part2 = $this->solve_part2($data, $this->DIRS) + $part1;

        return [$part1, $part2];
    }

    /**
     * Solves part 1 of the problem
     *
     * @param array $grid (pointer) The grid to process
     * @param array $dirs (pointer) The directions to check
     * @return int The number of rolls removed
     */
    private function solve_part1(&$grid, &$dirs) {
        // Save the rolls to an array that we can get the length of
        // for the answer and also use to modify the grid for part 2
        $rolls = [];

        // Loop through each point on the grid
        $y_len = count($grid);
        $x_len = count($grid[0]);
        for ($y = 0; $y < $y_len; $y++) {
            for ($x = 0; $x < $x_len; $x++) {
                // Guard clause to skip processing if it's not a roll
                if ($grid[$y][$x] !== "@") { continue; }

                // Keep track of how many adjacent squares have rolls
                $adjacent = 0;

                // Checking whether the current roll is on a grid boundary
                // to determine if we need a bounds check for the adjacent 
                // points saves between 20-35% time
                $check_bounds = Utils::is_on_grid_border($grid, $x, $y);
                foreach ($dirs as $dir) {
                    // Save each adjacent square as new coordinates relative to the current roll
                    $ax = $x + $dir[0];
                    $ay = $y + $dir[1];

                    // Guard clause to prevent processing any adjacent squares that
                    // are outside the grid
                    if ($check_bounds && Utils::is_outside_grid_bounds($grid, $ax, $ay)) {
                        continue;
                    }

                    // Increment how many adjacent rolls there are if we find a roll
                    if ($grid[$ay][$ax] === "@") {
                        $adjacent++;
                    }
                } // End the adjacent grid points loop

                // Save the roll to the $rolls array for further processing later
                if ($adjacent < 4) {
                    $rolls[] = [$y, $x];
                }
            } // End the grid x loop
        } // End the grid y loop
        
        // Update the grid to remove the rolls that were found
        // during this run
        $grid = $this->update_grid($grid, $rolls);

        // Return the length of the array of removed rolls as the answer for part 1
        return count($rolls); 
    }

    /**
     * Solves part 2 of the problem
     *
     * @param array $grid (pointer) The grid to process
     * @param array $dirs (pointer) The directions to check
     * @return int The number of rolls removed in part 2
     */
    private function solve_part2(&$grid, &$dirs) {
        // Keep track of how many rolls are removed in total
        $total_removed = 0;

        // Initialize removed to infinity so we can enter the loop
        $removed = INF;

        // As long as part 1 is removing rolls, keep running it
        while ($removed > 0) {
            $removed = $this->solve_part1($grid, $dirs);
            $total_removed += $removed;
        } // End the loop

        // Return the total number of removed rolls as part of the answer for part 2
        // We add part 1's answer to this in the main solve() method
        return $total_removed;
    }

    /**
     * Updates the grid by removing the specified rolls
     *
     * @param array $grid (pointer) The grid to update
     * @param array $rolls(pointer) The rolls to remove
     * @return array The updated grid
     */
    private function update_grid(&$grid, &$rolls) {
        for ($point = 0; $point < count($rolls); $point++) {
            $y = $rolls[$point][0];
            $x = $rolls[$point][1];
            $grid[$y][$x] = ".";
        }
        return $grid;
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
