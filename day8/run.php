<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$part1 = 0;

foreach ($lines as $line) {
    [$in, $out] = explode(' | ', $line);
    $inpairs = explode(' ', $in);
    $outpairs = explode(' ', $out);

    foreach ($outpairs as $outpair) {
        if (strlen($outpair) < 5) {
            $part1++;
        }
        if (strlen($outpair) == 7) {
            $part1++;
        }
    }
}

p('Part 1: ' . $part1);

$part2 = 0;
function remove_chars_from_locations($chars, $locations, $options)
{
    for ($i = 0; $i < strlen($chars); $i++) {
        foreach ($locations as $location) {
            $options[$location] = str_replace($chars[$i], '', $options[$location]);
        }
    }
    return $options;
}


foreach ($lines as $line) {
    [$in, $out] = explode(' | ', $line);
    $inpairs = explode(' ', $in);
    $outpairs = explode(' ', $out);

    $options = array_fill(0, 7, 'abcdefg');
    /**
     *  0000
     * 1    2
     * 1    2
     *  3333
     * 4    5
     * 4    5
     *  6666
     */

    foreach ($inpairs as $inpair) {
        if (strlen($inpair) === 2) { // 1
            $options = remove_chars_from_locations($inpair, [0, 1, 3, 4, 6], $options);
        }
        if (strlen($inpair) === 3) { // 7
            $options = remove_chars_from_locations($inpair, [1, 3, 4, 6], $options);
        }
        if (strlen($inpair) === 4) { // 4
            $options = remove_chars_from_locations($inpair, [0, 4, 6], $options);
        }
    }

    // We now have position 4 and 6 down to the same 2 letter combo so they should be there
    $options = remove_chars_from_locations($options[4], [0, 1, 2, 3, 5], $options);
    // Position 0 is now set to 1 option, remove it from others
    $options = remove_chars_from_locations($options[0], [1, 2, 3, 4, 5, 6], $options);
    // We now have position 1 and 3 down to the same 2 letter combo so they should be there
    $options = remove_chars_from_locations($options[1], [0, 2, 4, 5, 6], $options);

    // Positions with 2 pairs left: 1-3 & 2-5 & 4-6
    foreach ($inpairs as $inpair) {

        // These should all have position 3 set, but not 1
        if (strlen($inpair) === 5) { // 2, 3, 5
            // So if char 0 doesn't exist char 0 should be on position 1
            if (!str_contains($inpair, $options[1][0])) {
                $pos1 = $options[1][0];
            }
            // But if char 1 doesn't exist char 1 should be on position 1
            if (!str_contains($inpair, $options[1][1])) {
                $pos1 = $options[1][1];
            }

        }

        // These should all have position 6 set, but not 4
        if (strlen($inpair) === 5) { // 2, 3, 5
            // So if char 0 doesn't exist char 0 should be on position 4
            if (!str_contains($inpair, $options[4][0])) {
                $pos4 = $options[4][0];
            }
            // But if char 1 doesn't exist char 1 should be on position 4
            if (!str_contains($inpair, $options[4][1])) {
                $pos4 = $options[4][1];
            }
        }

        // These should all have position 5 set, but not 2
        if (strlen($inpair) === 6) { // 0, 6, 9
            // So if char 0 doesn't exist char 0 should be on position 2
            if (!str_contains($inpair, $options[2][0])) {
                $pos2 = $options[2][0];
            }
            // But if char 1 doesn't exist char 1 should be on position 2
            if (!str_contains($inpair, $options[2][1])) {
                $pos2 = $options[2][1];
            }
        }
    }
    $options[1] = $pos1;
    $options[2] = $pos2;
    $options[4] = $pos4;

    // Since we've figured out position 1, we can fix position 3 as well
    $options = remove_chars_from_locations($options[1], [3], $options);
    // Since we've figured out position 4, we can fix position 6 as well
    $options = remove_chars_from_locations($options[4], [6], $options);
    // Since we've figured out position 2, we can fix position 5 as well
    $options = remove_chars_from_locations($options[2], [5], $options);

    // Options now contains the remapping

    // Let's figure out what the output numbers are
    $res = '';
    foreach ($outpairs as $outpair) {
        $outpair_chars = str_split($outpair);
        $outpair_numbers = [];
        foreach ($outpair_chars as $c) {
            $outpair_numbers[] = array_search($c, $options);
        }
        sort($outpair_numbers);

        switch ($outpair_numbers) {
            case [0, 1, 2, 4, 5, 6]:
                $res .= '0';
                break;
            case [2, 5]:
                $res .= '1';
                break;
            case [0, 2, 3, 4, 6]:
                $res .= '2';
                break;
            case [0, 2, 3, 5, 6]:
                $res .= '3';
                break;
            case [1, 2, 3, 5]:
                $res .= '4';
                break;
            case [0, 1, 3, 5, 6]:
                $res .= '5';
                break;
            case [0, 1, 3, 4, 5, 6]:
                $res .= '6';
                break;
            case [0, 2, 5]:
                $res .= '7';
                break;
            case [0, 1, 2, 3, 4, 5, 6]:
                $res .= '8';
                break;
            case [0, 1, 2, 3, 5, 6]:
                $res .= '9';
                break;
            default:
                echo 'Failed figuring out number ' . $outpair . "\n";
                var_dump($outpair_numbers);
                $res .= '.';
                break;
        }
    }
    $part2 += intval($res);
}

p('Part 2: ' . $part2);
