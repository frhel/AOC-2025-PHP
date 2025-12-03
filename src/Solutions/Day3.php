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
        $n_cells = 12;
        foreach ($data as $row) {
            $last_idx = 0;
            $total = "";
            for ($i = 1; $i <= 12; $i++) {
                $curr_end = count($row) - $n_cells + $i - 1;
                $max = -1;
                for ($n = $last_idx; $n <= $curr_end; $n++) {
                    if ($row[$n] > $max) {
                        $max = $row[$n];
                        $last_idx = $n + 1;
                    }
                }
                $total .= $max;
            }
            $total_joltage += (int)$total;
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
