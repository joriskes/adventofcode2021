<?php
require __DIR__ . '/../bootstrap.php';

define("BINGO_BOARD_SIZE", 5);

class BingoBoardSpace
{
  private int $number;
  private bool $marked;

  public function __construct($number)
  {
    $this->number = $number;
    $this->marked = false;
  }

  public function getNumber(): int
  {
    return $this->number;
  }

  public function isMarked(): bool
  {
    return $this->marked;
  }

  public function setMarked(bool $marked): void
  {
    $this->marked = $marked;
  }
}

class BingoBoard
{
  /**
   * @var BingoBoardSpace[][]
   */
  private array $spaces;

  public function __construct($lines)
  {
    $this->spaces = [];
    foreach ($lines as $index => $line) {
      $this->spaces[$index] = [];
      $numbers = explode(' ', trim($line));
      foreach ($numbers as $number) {
        if (trim($number) !== '') {
          array_push($this->spaces[$index], new BingoBoardSpace($number));
        }
      }
    }
  }

  public function strikeNumber($number)
  {
    foreach ($this->spaces as $line) {
      foreach ($line as $space) {
        if ($space->getNumber() === $number) {
          $space->setMarked(true);
        }
      }
    }
  }

  public function isWinner()
  {
    foreach ($this->spaces as $line) {
      $markedCount = 0;
      foreach ($line as $space) {
        if ($space->isMarked()) {
          $markedCount++;
        }
      }
      if ($markedCount === BINGO_BOARD_SIZE) {
        return true;
      }
    }

    for ($i = 0; $i < BINGO_BOARD_SIZE; $i++) {
      $markedCount = 0;
      foreach ($this->spaces as $line) {
        if ($line[$i]->isMarked()) {
          $markedCount++;
        }
      }
      if ($markedCount === BINGO_BOARD_SIZE) {
        return true;
      }
    }

    return false;
  }

  public function sumUnmarked()
  {
    $res = 0;
    foreach ($this->spaces as $line) {
      foreach ($line as $space) {
        if (!$space->isMarked()) {
          $res += $space->getNumber();
        }
      }
    }
    return $res;
  }

  public function printBoard()
  {
    foreach ($this->spaces as $line) {
      foreach ($line as $space) {
        $n = $space->getNumber();
        if ($n < 10) echo ' ';
        echo $n;
        if ($space->isMarked()) echo '* ';
        else echo '  ';
      }
      echo "\n";
    }
    echo "\n";
  }
}

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$numbers = explode(',', array_shift($lines));

$bingoBoards = [];

// Create bingo boards
while (count($lines) > BINGO_BOARD_SIZE - 1) {
  $subLines = [];
  while (count($subLines) < BINGO_BOARD_SIZE) {
    $line = array_shift($lines);
    if (!empty($line)) {
      array_push($subLines, $line);
    }
  }
  array_push($bingoBoards, new BingoBoard($subLines));
}

$winningBoard = null;
$losingBoard = null;
$part1LastNumber = -1;
$part2LastNumber = -1;

while (count($numbers) > 0 && count($bingoBoards) > 0) {
  $number = intval(trim(array_shift($numbers)));
  foreach ($bingoBoards as $i => $bingoBoard) {
    $bingoBoards[$i]->strikeNumber($number);
    if ($bingoBoards[$i]->isWinner()) {
      if (!isset($winningBoard)) {
        $winningBoard = $bingoBoards[$i];
        $part1LastNumber = $number;
      }
      $losingBoard = $bingoBoards[$i];
      $part2LastNumber = $number;
    }
  }
  // In order to keep the board array intact while striking numbers we
  // filter after the foreach, you don't want to remove while looping
  $bingoBoards = array_filter($bingoBoards, function ($b) {
    return !$b->isWinner();
  });
}

$part1 = $winningBoard->sumUnmarked() * $part1LastNumber;
$part2 = $losingBoard->sumUnmarked() * $number;

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
