<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2025/day/1
// Solution by: https://github.com/frhel (Fry)
// Part1: 
// Part2:
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2025php\Solutions;

use frhel\adventofcode2025php\Tools\Timer;
use frhel\adventofcode2025php\Tools\Prenta;
use frhel\adventofcode2025php\Tools\Utils;

class Day1 extends Day
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
        $part1 = $this->part1($data, $part1, 50);
        $part2 = $this->part2($data, $part2, 50);
  
        return [$part1, $part2];
    }

    private function part1($data, &$part1, $position) {
        foreach ($data as $line) {
            $position = $this->adjust_position($line, $position);
            $position = $position % 100;
            if ($position < 0) {
                $position += 100;
            }
            if ($position === 0) {
                $part1++;
            }
        }
        return $part1;
    }

    private function part2($data, &$part2, $position) {
        foreach ($data as $i => $line) {
            $dir = $line['dir'];
            $steps = $line['steps'];
            $dir_modifier = 1;
            if ($dir === 'L') {
                $dir_modifier = -1;
            }

            for ($s = 0; $s < $steps; $s++) {
                $position += $dir_modifier;
                if ($position >= 100) {
                    $position -= 100;
                } elseif ($position < 0) {
                    $position += 100;
                }
                if ($position === 0) {
                    $part2++;
                }
            }
            
        }
        return $part2;
    }

    protected function adjust_position($line, $position) {
        if ($line['dir'] === 'L') {
            $position -= $line['steps'];
        } else {
            $position += $line['steps'];
        }
        return $position;
    }

    /**
     * Parses the input data into a usable format
     *
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {
        $data = preg_split('/\r\n|\r|\n/', $data);

        foreach ($data as &$line) {
            $line = trim($line);
            $split = preg_split('/(?=[LR])/', $line, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($split as $part) {
                $line = [
                    'dir' => $part[0],
                    'steps' => (int)substr($part, 1),
                ];
            }
        }

        return $data;
    }
}
