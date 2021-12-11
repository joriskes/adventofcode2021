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

class color
{
    public const black = '30';
    public const red = '31';
    public const green = '32';
    public const brown = '33';
    public const blue = '34';
    public const purple = '35';
    public const cyan = '36';
    public const white = '37';
}

/**
 * Returns a string console colorized to the supplied color
 * @param string $color (color::black)
 * @param string $str
 * @return string
 */
function console_color($color, $str)
{
    return "\033[" . $color . "m" . $str . "\033[37m";
}
