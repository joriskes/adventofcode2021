<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

/**
 * Returns the control character, if it is open or close, part1 score and part 2 score
 * @param $char
 * @return [string, bool, int, int]
 */
function char_index($char): array
{
    if ($char == '(') return ['(', true, 3, 1];
    if ($char == ')') return ['(', false, 3, 1];
    if ($char == '[') return ['[', true, 57, 2];
    if ($char == ']') return ['[', false, 57, 2];
    if ($char == '{') return ['{', true, 1197, 3];
    if ($char == '}') return ['{', false, 1197, 3];
    if ($char == '<') return ['<', true, 25137, 4];
    if ($char == '>') return ['<', false, 25137, 4];

    echo 'Unhandled char!' . $char . "\n";
    die();
}

$part1 = 0;
$part2 = 0;

$scores = [];
foreach ($lines as $line) {
    $pair_builder = '';
    $chars = str_split($line);
    $corrupted = false;
    foreach ($chars as $char_actual) {
        [$char, $isOpen, $score] = char_index($char_actual);
        if ($isOpen) {
            $pair_builder .= $char;
        } else {
            $expect = substr($pair_builder, -1);
            if ($expect === $char) {
                $pair_builder = substr($pair_builder, 0, -1);
            } else {
                // p($line . ' expected ' . $expect . ' but got ' . $char_actual);
                $part1 += $score;
                $corrupted = true;
                break;
            }
        }
    }
    if (!$corrupted) {
        $todo = array_reverse(str_split($pair_builder));
        $score = 0;
        foreach ($todo as $todo_char) {
            [, , , $char_score] = char_index($todo_char);
            $score = $score * 5;
            $score += $char_score;
        }
        $scores[] = $score;
    }
}
sort($scores);
$part2 = $scores[count($scores) / 2];

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
