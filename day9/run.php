<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);
foreach ($lines as $line) {
    $floor[] = array_map('intval', str_split($line));
}

function val($floor, $x, $y)
{
    $height = count($floor);
    $width = count($floor[0]);
    if ($x >= 0 && $x < $width && $y >= 0 && $y < $height) {
        return $floor[$y][$x];
    }
    return 9;
}

$counted = [];
function count_basin($floor, $x, $y)
{
    global $counted;
    $counted_index = $y * count($floor[0]) + $x;
    if (in_array($counted_index, $counted)) {
        return 0;
    }
    $counted[] = $counted_index;
    $h = val($floor, $x, $y);
    if ($h > 8) {
        return 0;
    }
    $sum = 1;
    if (val($floor, $x + 1, $y) > $h) $sum += count_basin($floor, $x + 1, $y);
    if (val($floor, $x - 1, $y) > $h) $sum += count_basin($floor, $x - 1, $y);
    if (val($floor, $x, $y + 1) > $h) $sum += count_basin($floor, $x, $y + 1);
    if (val($floor, $x, $y - 1) > $h) $sum += count_basin($floor, $x, $y - 1);
    return $sum;
}

$part1 = 0;
$part2 = 0;

$basins = [];
foreach ($floor as $y => $f) {
    foreach ($f as $x => $h) {
        if ($h < val($floor, $x + 1, $y) &&
            $h < val($floor, $x - 1, $y) &&
            $h < val($floor, $x, $y + 1) &&
            $h < val($floor, $x, $y - 1)) {
            $part1 += $h + 1;

            $basins[] = count_basin($floor, $x, $y);
        }
    }
}
rsort($basins);
$part2 = $basins[0] * $basins[1] * $basins[2];

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
