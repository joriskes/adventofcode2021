<?php
require __DIR__ . '/../bootstrap.php';

class SubmarinePart1
{
  private int $position = 0;
  private int $depth = 0;

  public function movePosition(int $distance)
  {
    $this->position += $distance;
  }

  public function moveDepth(int $distance)
  {
    $this->depth += $distance;
  }

  public function getDepth(): int
  {
    return $this->depth;
  }

  public function getPosition(): int
  {
    return $this->position;
  }
}

class SubmarinePart2
{
  private int $position = 0;
  private int $depth = 0;
  private int $aim = 0;

  public function movePosition(int $distance)
  {
    $this->position += $distance;
    $this->depth += $this->aim * $distance;
  }

  public function moveDepth(int $distance)
  {
    $this->aim += $distance;
  }

  public function getDepth(): int
  {
    return $this->depth;
  }

  public function getPosition(): int
  {
    return $this->position;
  }
}


$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$sub1 = new SubmarinePart1();
$sub2 = new SubmarinePart2();
foreach ($lines as $line) {
  [$keyword, $distance] = explode(' ', $line);
  switch ($keyword) {
    case 'forward':
      $sub1->movePosition($distance ?? 0);
      $sub2->movePosition($distance ?? 0);
      break;
    case 'up':
      $sub1->moveDepth($distance * -1 ?? 0);
      $sub2->moveDepth($distance * -1 ?? 0);
      break;
    case 'down':
      $sub1->moveDepth($distance ?? 0);
      $sub2->moveDepth($distance ?? 0);
      break;
    default:
      p('Unknown keyword', $keyword);
  }
}

p('Part 1: ' . $sub1->getDepth() * $sub1->getPosition());
p('Part 2: ' . $sub2->getDepth() * $sub2->getPosition());
