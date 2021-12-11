<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$octos = [];
$flashed = [];
foreach ($lines as $y => $line) {
    $octos[$y] = array_map('intval', str_split($line));
}

function value($x, $y)
{
    global $octos;
    if ($x >= count($octos)) return -1;
    if ($y >= count($octos)) return -1;
    if ($x <= 0) return -1;
    if ($y <= 0) return -1;

    return $octos[$y][$x];
}

function increase_octo($x, $y)
{
    global $octos;
    if ($x >= count($octos)) return;
    if ($y >= count($octos)) return;
    if ($x < 0) return;
    if ($y < 0) return;

    $octos[$y][$x] = $octos[$y][$x] + 1;
}

$part1 = 0;
for ($step = 1; $step <= 1000; $step++) {
    $flashed = [];
    // First, increase all
    foreach ($octos as $y => $l) {
        $flashed[$y] = [];
        foreach ($l as $x => $octo) {
            $octos[$y][$x] = $octo + 1;
            $flashed[$y][$x] = false;
        }
    }

    // Flash train
    do {
        $flash = false;
        foreach ($octos as $y => $l) {
            foreach ($l as $x => $octo) {
                if ($octo > 9 && !$flashed[$y][$x]) {
                    if ($step <= 100) {
                        $part1++;
                    }
                    $flash = true;
                    $flashed[$y][$x] = true;
                    increase_octo($x - 1, $y - 1);
                    increase_octo($x - 1, $y + 0);
                    increase_octo($x - 1, $y + 1);
                    increase_octo($x + 0, $y - 1);
                    increase_octo($x + 0, $y + 0);
                    increase_octo($x + 0, $y + 1);
                    increase_octo($x + 1, $y - 1);
                    increase_octo($x + 1, $y + 0);
                    increase_octo($x + 1, $y + 1);
                }
            }
        }
    } while ($flash);

    // Reset flashers
    $all_flashed = true;
    foreach ($octos as $y => $l) {
        foreach ($l as $x => $octo) {
            if (!$flashed[$y][$x]) {
                $all_flashed = false;
            }
            if ($octo > 9) {
                $octos[$y][$x] = 0;
            }
        }
    }
    if ($all_flashed) {
        break;
    }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $step);
