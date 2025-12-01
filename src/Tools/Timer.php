<?php

namespace frhel\adventofcode2025php\Tools;

use DateTime;

class Timer {
    private float $startTime;
    private float $endTime;
    private array $checkpoints;

    function __construct() {
        $this->checkpoints = [];
        $this->start();
    }

    public function reset(): void {
        $this->startTime = 0;
        $this->endTime = 0;
        $this->checkpoints = [];
    }

    public function start(): void {
        $this->startTime = microtime(true);
    }

    public function stop(): string {
        $this->endTime = microtime(true);
        return $this->getElapsedTime();
    }

    public function getStartTime(): float {
        return $this->startTime;
    }

    public function getElapsedTime(): string {
        return $this->formatTimeValue($this->endTime - $this->startTime);
    }

    public function getStopTime(): float {
        return $this->endTime;
    }

    public function checkpoint(?string $name = null): void {
        if ($name) {
            $this->checkpoints[$name] = microtime(true);
        } else {
            $this->checkpoints[] = microtime(true);
        }
    }

    public function getCheckpoints(): array {
        return $this->checkpoints;
    }

    public function getLastCheckpoint(): float {
        return $this->checkpoints[count($this->checkpoints) - 1];
    }

    public function getCheckpoint($index): float {
        return $this->checkpoints[$index];
    }

    public function avg_time(): string {
        $times = $this->calc_elapsed_times();
        if (empty($times)) return '0µs';

        $total = array_sum($times);
        $avg = $total / count($times);

        return $this->formatTimeValue($avg);
    }

    public function median_time(): string {
        $times = $this->calc_elapsed_times();
        if (empty($times)) return '0µs';

        sort($times);
        $count = count($times);
        $median = $times[intdiv($count, 2)];

        return $this->formatTimeValue($median);
    }

    private function calc_elapsed_times(): array {
        $values = [];
        $last = $this->startTime;
        foreach ($this->checkpoints as $c) {
            $values[] = $c - $last;
            $last = $c;
        }
        return $values;
    }

    public function formatTime(float $start, ?float $end = null): string {
        if ($end === null) {
            $time = $start;
        } else {
            $time = $end - $start;
        }
        return $this->formatTimeValue($time);
    }

    private function formatTimeValue(float $time): string {
        if ($time < 0.001) {
            return $this->formatMicroseconds($time);
        } else if ($time < 1) {
            return $this->formatMilliseconds($time);
        } else {
            return $this->formatSeconds($time);
        }
    }

    public function formatMilliseconds(float $time): string {
        $time = $time * 1000;
        $time = number_format($time, 2, '.', '');
        return $time . 'ms';
    }

    public function formatMicroseconds(float $time): string {
        $time = $time * 1000000;
        $time = number_format($time, 0, '.', '');
        return $time . 'µs';
    }

    public function formatSeconds(float $time): string {
        $time = number_format($time, 2, '.', '');
        return $time . 's';
    }
}
