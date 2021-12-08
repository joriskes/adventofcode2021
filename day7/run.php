<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$crabs = input_first_line_to_int_array($input);

rsort($crabs);
$middle = round(count($crabs) / 2);
$median = $crabs[$middle - 1];

$part1 = 0;
foreach ($crabs as $crab) {
    $part1 += abs($crab - $median);
}
p('Part 1: ' . $part1);

// My math runs out here... brute force it
$max = 0;
$min = 999999999;
foreach ($crabs as $crab) {
    if ($crab > $max) $max = $crab;
    if ($crab < $min) $min = $crab;
}

$part2 = 99999999;
for ($i = $min; $i <= $max; $i++) {
    $sum = 0;
    foreach ($crabs as $crab) {
        $a = abs($crab - $i);
        $sum += ($a * ($a + 1)) / 2;
    }
    if ($sum < $part2) $part2 = $sum;
}
p('Part 2: ' . $part2);
