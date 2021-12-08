<?php
function p($txt)
{
    echo $txt . "\n";
}

/**
 * Takes the each line of input, trims it and returns as a string array
 * @param string $input
 * @return string[]
 */
function input_to_lines($input)
{
    return array_map("trim", explode("\n", trim($input)));
}

/**
 * Takes the first line of input, splits it on , and converts all resulting parts to integer
 * @param string $input
 * @return int[]
 */
function input_first_line_to_int_array($input)
{
    $lines = input_to_lines($input);
    $line = trim(array_pop($lines));
    return array_map('intval', explode(',', $line));
}
