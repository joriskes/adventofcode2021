<?php
require __DIR__ . '/../bootstrap.php';

class Line
{
    private int $fromX;
    private int $fromY;
    private int $toX;
    private int $toY;

    function parse($str)
    {
        $points = explode(' -> ', $str);
        if (count($points) !== 2) {
            return false;
        }
        [$this->fromX, $this->fromY] = array_map('trim', explode(',', $points[0]));
        [$this->toX, $this->toY] = array_map('trim', explode(',', $points[1]));
        // Swap if to is left / above from
        if ($this->fromX > $this->toX || $this->fromY > $this->toY) {
            $x = $this->fromX;
            $y = $this->fromY;
            $this->fromX = $this->toX;
            $this->fromY = $this->toY;
            $this->toX = $x;
            $this->toY = $y;
        }
        return true;
    }

    public function getFrom(): array
    {
        return [$this->fromX, $this->fromY];
    }

    public function getTo(): array
    {
        return [$this->toX, $this->toY];
    }

    public function drawOn($grid, $onlyPerpendicular)
    {
        $isPerpendicular = true;
        if (($this->fromX !== $this->toX) && ($this->fromY !== $this->toY)) {
            $isPerpendicular = false;
        }
        if ($onlyPerpendicular && !$isPerpendicular) {
            return $grid;
        }
        $res = $grid;
        if ($isPerpendicular) {
            for ($y = $this->fromY; $y <= $this->toY; $y++) {
                for ($x = $this->fromX; $x <= $this->toX; $x++) {
                    $res[$y][$x] = $res[$y][$x] + 1;
                }
            }
        } else {
            $penX = $this->fromX;
            $penY = $this->fromY;
            $stepX = $this->fromX > $this->toX ? -1 : 1;
            $stepY = $this->fromY > $this->toY ? -1 : 1;
            while ($penX !== $this->toX || $penY !== $this->toY) {
                $res[$penY][$penX] = $res[$penY][$penX] + 1;
                $penX += $stepX;
                $penY += $stepY;
            }
            $res[$penY][$penX] = $res[$penY][$penX] + 1;
        }
        return $res;
    }

    public function __toString(): string
    {
        return $this->fromX . ',' . $this->fromY . ' -> ' . $this->toX . ',' . $this->toY;
    }
}

// Parse input
$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);
$lineList = [];
foreach ($lines as $line) {
    $newLine = new Line();
    if ($newLine->parse($line)) {
        array_push($lineList, $newLine);
    }
}

// Determine grid size
$maxX = 0;
$maxY = 0;
/* @var $lineList Line[] */
foreach ($lineList as $line) {
    [$fx, $fy] = $line->getFrom();
    if ($fx > $maxX) $maxX = $fx;
    if ($fy > $maxY) $maxY = $fy;
    [$tx, $ty] = $line->getTo();
    if ($tx > $maxX) $maxX = $tx;
    if ($ty > $maxY) $maxY = $ty;
}

// Create grids
$gridPart1 = [];
$gridPart2 = [];
for ($height = 0; $height <= $maxY; $height++) {
    $gridPart1[$height] = array_fill(0, $maxX + 1, 0);
    $gridPart2[$height] = array_fill(0, $maxX + 1, 0);
}

// Draw lines on grid
/* @var $lineList Line[] */
foreach ($lineList as $line) {
    $gridPart1 = $line->drawOn($gridPart1, true);
    $gridPart2 = $line->drawOn($gridPart2, false);
}

// Count part 1
$part1 = 0;
foreach ($gridPart1 as $line) {
    foreach ($line as $c) {
        if ($c >= 2) {
            $part1++;
        }
    }
}

// Count part2
$part2 = 0;
foreach ($gridPart2 as $line) {
    foreach ($line as $c) {
        if ($c >= 2) {
            $part2++;
        }
    }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
