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

  public function drawSpot($grid, $x, $y)
  {
    $res = $grid;
    $l = str_split($res[$y]);
    $l[$x] = intval($l[$x]) + 1;
    $res[$y] = implode('', $l);
    return $res;
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
          $res = $this->drawSpot($res, $x, $y);
        }
      }
    } else {
      $penX = $this->fromX;
      $penY = $this->fromY;
      $stepX = $this->fromX > $this->toX ? -1 : 1;
      $stepY = $this->fromY > $this->toY ? -1 : 1;
      while ($penX !== $this->toX || $penY !== $this->toY) {
        $res = $this->drawSpot($res, $penX, $penY);
        $penX += $stepX;
        $penY += $stepY;
      }
      $res = $this->drawSpot($res, $penX, $penY);
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
  $gridPart1[$height] = str_pad('', $maxX + 1, '.');
  $gridPart2[$height] = str_pad('', $maxX + 1, '.');
}

// Draw lines on grid
/* @var $lineList Line[] */
foreach ($lineList as $line) {
  $gridPart1 = $line->drawOn($gridPart1, true);
  $gridPart2 = $line->drawOn($gridPart2, false);
}

// Print grid
//p('Part 1');
//foreach ($gridPart1 as $g) {
//  p($g);
//}
//p('Part 2');
//foreach ($gridPart2 as $g) {
//  p($g);
//}


// Count part 1
$part1 = 0;
foreach ($gridPart1 as $g) {
  $char = str_split($g);
  foreach ($char as $c) {
    if (intval($c) >= 2) {
      $part1++;
    }
  }
}

// Count part2
$part2 = 0;
foreach ($gridPart2 as $g) {
  $char = str_split($g);
  foreach ($char as $c) {
    if (intval($c) >= 2) {
      $part2++;
    }
  }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);

// Fun, draw it to image
$gd = imagecreatetruecolor($maxX, $maxY);
$green = imagecolorallocate($gd, 0, 255, 0);
$red = imagecolorallocate($gd, 255, 0, 0);

foreach ($gridPart2 as $y => $g) {
  for ($x = 0; $x < strlen($g); $x++) {
    $char = intval($g[$x]);
    if ($char == 1) {
      imagesetpixel($gd, round($x), round($y), $green);
    }
    if ($char > 1) {
      imagesetpixel($gd, round($x), round($y), $red);
    }
  }
}
imagepng($gd, __DIR__ . '/output.png');
imagedestroy($gd);
