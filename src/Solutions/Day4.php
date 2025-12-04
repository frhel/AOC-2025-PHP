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
        $part2 = $this->solve_part2($data, $this->DIRS) + $part1;

        return [$part1, $part2];
    }

    private function solve_part1(&$grid, &$dirs) {
        $rolls = [];
        foreach ($grid as $y=>$row) {
            foreach ($row as $x=>$col) {
                if ($grid[$y][$x] !== "@") {
                    continue;
                }
                $adjacent = 0;
                foreach ($dirs as $dir) {
                    $tx = $x + $dir[0];
                    $ty = $y + $dir[1];
                    if (Utils::is_within_grid_bounds($grid, $tx, $ty) && $grid[$ty][$tx] === "@") {
                        $adjacent++;
                    }
                }
                if ($adjacent < 4) {
                    $rolls[] = [$y, $x];
                }
            }
        }
        $grid = $this->update_grid($grid, $rolls);
        return count($rolls);
    }

    private function solve_part2(&$grid, &$dirs) {
        $total_removed = 0;
        $removed = INF;
        while ($removed > 0) {
            $removed = $this->solve_part1($grid, $dirs);
            $total_removed += $removed;
        }
        return $total_removed;
    }

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
