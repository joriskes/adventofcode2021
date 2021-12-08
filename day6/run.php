<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);
$line = trim(array_pop($lines));

$fish = array_map('intval', explode(',', $line));

// Run length encode, for speed
$rle_fish = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
foreach ($fish as $f) {
    $rle_fish[$f] = $rle_fish[$f] + 1;
}

$part1 = 0;
$part2 = 0;

$days_part1 = 80;
$days_part2 = 256;
for ($i = 1; $i <= $days_part2; $i++) {
    $new_rle_fish = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
    foreach ($rle_fish as $key => $f) {
        $index = $key;
        if ($index == 0) {
            $new_rle_fish[6] = $new_rle_fish[6] + $f;
            $new_rle_fish[8] = $new_rle_fish[8] + $f;
        } else {
            $new_rle_fish[$index - 1] = $new_rle_fish[$index - 1] + $rle_fish[$key];
        }
    }
    $rle_fish = $new_rle_fish;
    if ($i === $days_part1) {
        foreach ($rle_fish as $f) {
            $part1 += $f;
        }
    }
}
foreach ($rle_fish as $f) {
    $part2 += $f;
}
p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
