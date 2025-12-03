<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2025/day/3
// Solution by: https://github.com/frhel (Fry)
// Part1:
// Part2:
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2025php\Solutions;


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

    static function solve_part1($data) {
        $total_joltage = 0;
        foreach ($data as $row) {
            $row_len = count($row);
            $max = -1;
            for ($i = 0; $i < $row_len - 1; $i++) {
                for ($j = $i+1; $j < $row_len; $j++) {
                    $num = $row[$i] * 10 + $row[$j];
                    if ($num > $max) {
                        $max = $num;
                    }
                }
            }
            $total_joltage += $max;
        }
        return $total_joltage;
    }

    static function solve_part2($data) {
        $total_joltage = 0;
        $n_batteries = 12; // We're only switching on 12 batteries
        foreach ($data as $row) {

            // Save the last index we used to avoid reusing batteries
            $last_idx = 0;
            $total = 0;
            $row_len = count($row);

            // Since we only need to find 12 batteries, we can limit our search to 12 iterations
            for ($i = 1; $i <= 12; $i++) {

                // For every iteration we need to make sure that we leave enough batteries
                // at the end for us to turn on. We do this by limiting the search space
                // to the length of the row minus the number of batteries left to turn on plus
                // the current iteration index (minus one for zero indexing)
                $curr_end = $row_len - $n_batteries + $i - 1;

                // The search space for each iteration depends on the last index we used
                // and the current end we calculated above.
                // All we have to do is find the max digit within that span
                $max = -1;                
                for ($n = $last_idx; $n <= $curr_end; $n++) {                    
                    if ($row[$n] > $max) {                        
                        $max = $row[$n];
                        $last_idx = $n + 1; // Keep track of our current position
                    }
                }
                $total = $total * 10 + $max; // Concat the max digit to our total as a string
            }
            $total_joltage += $total;
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
            return array_map('intval', str_split($line));
        }, $data);

        return $data;
    }
}
