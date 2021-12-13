<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$links = [];
foreach ($lines as $line) {
    [$from, $to] = explode('-', $line);
    $links[$from][] = $to;
    $links[$to][] = $from;
}

function count_small_visits(array $lower_paths)
{
    return count($lower_paths) - count(array_unique($lower_paths));
}

function traverse_nodes(array $links, array &$paths, string $current, int $max_visits, int $attempt = 0): int
{
    $path = $paths[$attempt] ?? [];
    $path[] = $current;
    if ($current === 'end') {
        $paths[$attempt] = $path;
        return $attempt + 1;
    }
    $lower_paths = array_filter($path, function ($p) {
        return strtolower($p) === $p;
    });
    foreach ($links[$current] as $cave) {
        if ($cave === 'start' || (strtolower($cave) === $cave && count_small_visits($lower_paths) >= $max_visits)) {
            continue;
        }
        if (!in_array($paths, $path)) {
            $paths[$attempt] = $path;
            $attempt = traverse_nodes($links, $paths, $cave, $max_visits, $attempt);
        }
    }
    unset($paths[$attempt]);
    return $attempt;
}


$paths = [];
$part1 = traverse_nodes($links, $paths, 'start', 1);
p('Part 1: ' . $part1);

$paths = [];
$part2 = traverse_nodes($links, $paths, 'start', 2);
p('Part 2: ' . $part2);
