<?php
require __DIR__ . '/../bootstrap.php';

class Dot
{
    private int $x;
    private int $y;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }


    public function fold($axis, $pos)
    {
        if ($this->$axis > $pos) {
            $this->$axis = $this->$axis - (($this->$axis - $pos) * 2);
        }
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }
}

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$dot_mode = true;
$dots = [];
$folds = [];
$max_x = 0;
$max_y = 0;
foreach ($lines as $line) {
    if (empty($line)) {
        $dot_mode = false;
    } else {
        if ($dot_mode) {
            [$x, $y] = explode(',', $line);
            $dots[] = new Dot($x, $y);
            if ($x > $max_x) $max_x = $x;
            if ($y > $max_y) $max_y = $y;
        } else {
            $folds[] = explode('=', str_replace('fold along ', '', $line));
        }
    }
}

$executionStartTime = microtime(true);
foreach ($folds as $index => $fold) {
    /* @var $dots Dot[] */
    $max_x = 0;
    $max_y = 0;
    foreach ($dots as $dot) {
        $dot->fold($fold[0], $fold[1]);
        if ($dot->getX() > $max_x) $max_x = $dot->getX();
        if ($dot->getY() > $max_y) $max_y = $dot->getY();
    }
    // Part 1 takes place after fold 1
    if ($index === 0) {
        $part1 = 0;
        $found = [];
        foreach ($dots as $d) {
            $s = $d->getY() * $max_x + $d->getX();
            if (!in_array($s, $found)) {
                $part1++;
                $found[] = $s;
            }
        }
        p('Part 1: ' . $part1);
    }
}

echo 'Part 2: ' . "\n";
for ($y = 0; $y <= $max_y; $y++) {
    for ($x = 0; $x <= $max_x; $x++) {
        $char = '.';
        foreach ($dots as $dot) {
            if (($dot->getX() === $x) && ($dot->getY() === $y)) {
                $char = '#';
                $part1++;
                break;
            }
        }
        echo $char;
    }
    echo "\n";
}
