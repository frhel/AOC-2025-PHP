<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2025/day/2
// Solution by: https://github.com/frhel (Fry)
// Part1:
// Part2:
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2025php\Solutions;

use frhel\adventofcode2025php\Tools\Prenta;
use frhel\adventofcode2025php\Tools\Timer;

class Range {
    public $lower;
    public $upper;
}

class Day2 extends Day
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

        [$part1, $part2] = $this->solve_both($data, $part1, $part2);
        return [$part1, $part2];
    }

    protected function solve_both($data, &$part1, &$part2) {
        $id = "";
        foreach ($data as $range) {
            // Walk through all IDs in current range
            for ($i = $range->lower; $i <= $range->upper; $i++) {
                // Convert ID to string for easier processing
                $id = (string)$i;
                $result = $this->contains_sequence($id, strlen($id));

                // 0 = no sequence
                // 1 = sequence of the full length (part 1)
                // 2 = smaller sequence (part 2)
                if ($result === 0) {
                    continue;
                } else if ($result === 1) {
                    $part1 += $i;
                }
                $part2 += $i;
            }
        }
        return [$part1, $part2];
    }

    protected function contains_sequence($id, $idLen) {
        // Save size of half the id as a starting point
        // to reduce the search space. If we find a sequence
        // of the largest possible size, we can stop immediately.
        $half = (int)($idLen / 2);
        $size = $half;

        while ($size > 0) {
            // If id length is not divisible by size, skip this size
            if ($idLen % $size !== 0) {
                $size--;
                continue;
            }

            // Grab the base substring and compare it to the rest
            $base = substr($id, 0, $size);

            // Iterate over the number of expected sequences. We can skip the first
            // one as it's the base we're comparing to.
            for ($chunk_count = ($idLen / $size); $chunk_count > 1; $chunk_count--) {
                // Compare to the substring at the current position.
                // Continue the outer while loop if we find a mismatch.
                if (substr($id, ($chunk_count - 1) * $size, $size) !== $base) {
                    $size--;
                    continue 2;
                }
            }

            // If we reach here, we found a matching sequence and can return true.
            if ($size === $half) {
                return 1;
            }
            return 2;
        }
        return 0;
    }

    /**
     * Parses the input data into a usable format
     *
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {
        $data = preg_split('/,/', $data);

        foreach ($data as $key => $value) {
            $range = new Range();
            [$range->lower, $range->upper] = preg_split('/-/', $value);
            $data[$key] = $range;
        }

        return $data;
    }
}
