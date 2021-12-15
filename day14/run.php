<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);
$start_polymer = array_shift($lines);
array_shift($lines);

$instructions = [];
foreach ($lines as $line) {
    [$pair, $insert] = explode(' -> ', $line);
    $instructions[$pair] = $insert;
}

function add_char(&$res, $char)
{
    if (isset($res[$char])) {
        $res[$char]++;
    } else {
        $res[$char] = 1;
    }
}

function sum_counts($counts1, $counts2)
{
    $res = $counts1;
    foreach ($counts2 as $char => $count) {
        if (!isset($res[$char])) {
            $res[$char] = $count;
        } else {
            $res[$char] += $count;
        }
    }
    return $res;
}

$cache = [];
function run_pair($pair, $instructions, $depth)
{
    global $cache;
    if (($depth % 10 === 0) && (isset($cache[$depth][$pair]))) {
        return $cache[$depth][$pair];
    }
    $insert = $instructions[$pair];
    if ($depth == 1) {
        $res = [];
        add_char($res, $insert);
        add_char($res, $pair[0]);
        return $res;
    }
    $res1 = run_pair($pair[0] . $insert, $instructions, $depth - 1);
    $res2 = run_pair($insert . $pair[1], $instructions, $depth - 1);
    $res = sum_counts($res1, $res2);
    if ($depth % 10 === 0) {
        $cache[$depth][$pair] = $res;
    }
    return $res;
}

$res = [];
for ($i = 1; $i < strlen($start_polymer); $i++) {
    $res = sum_counts($res, run_pair($start_polymer[$i - 1] . $start_polymer[$i], $instructions, 10));
}
$res[$start_polymer[strlen($start_polymer) - 1]]++;

rsort($res);
$part1 = $res[0] - $res[count($res) - 1];
p('Part 1: ' . $part1);


$res = [];
for ($i = 1; $i < strlen($start_polymer); $i++) {
    $res = sum_counts($res, run_pair($start_polymer[$i - 1] . $start_polymer[$i], $instructions, 40));
}
$res[$start_polymer[strlen($start_polymer) - 1]]++;

rsort($res);
$part2 = $res[0] - $res[count($res) - 1];
p('Part 2: ' . $part2);
