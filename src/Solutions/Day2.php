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
use frhel\adventofcode2025php\Tools\Utils;

use parallel\Runtime;

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

        $numCores = 4;
        $runtimes= [];
        for ($i = 0; $i < $numCores; $i++) {
            $runtimes[$i] = new Runtime();
        }
        $futures = [];
        $runtimeIdx = 0;

        foreach ($data as $range) {
            if (count($futures) >= $numCores) {
                $future = array_shift($futures);
                [$p1, $p2] = $future->value();
                $part1 += $p1;
                $part2 += $p2;
            }

            $futures[] = $runtimes[$runtimeIdx % $numCores]->run(function($range) {
                require 'vendor/autoload.php';
                return Day2::process_range($range);
            }, [$range, $autoloadPath]);
            $runtimeIdx++;
        }

        foreach ($futures as $future) {
            [$p1, $p2] = $future->value();
            $part1 += $p1;
            $part2 += $p2;
        }

        return [$part1, $part2];
    }

    public function process_range($range) {
        $part1 = 0;
        $part2 = 0;

        for ($i = $range->lower; $i <= $range->upper; $i++) {
            $id = (string)$i;
            [$has_sequence, $is_half] = $this->contains_sequence($id);
            if ($has_sequence) {
                if ($is_half && strlen($id) % 2 === 0) {
                    $part1 += $i;
                }
                $part2 += $i;
            }
        }

        return [$part1, $part2];
    }

    protected function contains_sequence($id) {
        $half = (int) floor(strlen($id) / 2);
        $size = $half;
        while ($size > 0) {
            $valid = true;
            if (strlen($id) % $size !== 0) {
                $size--;
                continue;
            }
            $base = substr($id, 0, $size);
            for ($i = ((int)strlen($id) / $size); $i > 0; $i--) {
                if (substr($id, ($i - 1) * $size, $size) !== $base) {
                    $valid = false;
                    break;
                }
            }
            if ($valid) {
                if ($size === $half) {
                    return [true, true];
                }
                return [true, false];
            }
            $size--;
        }
    }

    protected function is_half_sequence($id) {
        $half = (int) (strlen($id) / 2);
        return substr($id, 0, $half) === substr($id, -$half);
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
