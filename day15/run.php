<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);
$height = count($lines);
$width = strlen($lines[0]);

$cave = [];

function to_index($x, $y, $width, $height)
{
    if ($x >= $width) return -1;
    if ($y >= $height) return -1;
    if ($x < 0) return -1;
    if ($y < 0) return -1;
    // echo $x . ',' . $y . ' = ' . ($y * $width + $x) . "/n";
    return $y * $width + $x;
}

function from_index($index, $width)
{
    $y = floor($index / $width);
    return [$index - ($y * $width), $y];
}

foreach ($lines as $y => $line) {
    $nums = array_map('intval', str_split($line));
    foreach ($nums as $x => $num) {
        $cave[to_index($x, $y, $width, $height)] = $num;
    }
}

// Dijkstra implementation
function dijkstra($cave, $start, $end, $width, $height)
{
    // Set all caves unvisited
    $unvisited = $cave;

    // Set all distances to huge
    $distances = [];
    foreach ($cave as $pos => $num) {
        $distances[$pos] = 999999;
    }

    // Start distance = 0
    $distances[$start] = 0;

    $current = $start;
    $res = 0;

    // Speed up
    $unvisited_with_distance = [];

    while (count($unvisited) > 0) {

        $dist = $distances[$current];
        [$x, $y] = from_index($current, $width);
        if ($current == $end) {
            $res = $dist;
            break;
        }

        // Check all perpendical neighbours
        $indices_to_check = [
            to_index($x - 1, $y, $width, $height),
            to_index($x + 1, $y, $width, $height),
            to_index($x, $y - 1, $width, $height),
            to_index($x, $y + 1, $width, $height)
        ];
        foreach ($indices_to_check as $to_check) {
            if (isset($unvisited[$to_check])) {
                $dist_to_this = $unvisited[$to_check] + $dist;
                // If a neighbour is closer now, update it
                if ($distances[$to_check] > $dist_to_this) {
                    $unvisited_with_distance[$to_check] = 1;
                    $distances[$to_check] = $dist_to_this;
                }
            }
        }
        // Set current visited
        unset($unvisited[$current]);
        unset($unvisited_with_distance[$current]);

        // Find lowest unvisited distance amongst all unvisited
        $lowest = 999999;
        $lowest_index = -1;
        foreach ($unvisited_with_distance as $key => $value) {
            if ($distances[$key] < $lowest) {
                $lowest = $distances[$key];
                $lowest_index = $key;
            }
        }
        if ($lowest_index === -1) {
            break;
        } else {
            $current = $lowest_index;
        }
    }
    return $res;
}

function expand5($cave, $width, $height)
{
    $cave5 = [];
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $index = to_index($x, $y, $width, $height);

            for ($ex = 0; $ex < 5; $ex++) {
                for ($ey = 0; $ey < 5; $ey++) {
                    $index5 = to_index($x + $ex * $width, $y + $ey * $height, $width * 5, $height * 5);
                    $cave5[$index5] = ((($cave[$index] + $ex + $ey) - 1) % 9) + 1;
                }
            }
        }
    }
    return $cave5;
}

$part1 = dijkstra($cave, 0, to_index($width - 1, $height - 1, $width, $height), $width, $height);
p('Part 1: ' . $part1);

$cave5 = expand5($cave, $width, $height);
$width *= 5;
$height *= 5;
$part2 = dijkstra($cave5, 0, to_index($width - 1, $height - 1, $width, $height), $width, $height);
p('Part 2: ' . $part2);
