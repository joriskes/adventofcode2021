<?php
require __DIR__ . '/../bootstrap.php';

function getBitCounts($lines)
{
  $countAll = [];
  for ($i = 0; $i < strlen($lines[0]); $i++) {
    $countAll[$i] = [0, 0];
  }
  foreach ($lines as $line) {
    for ($i = 0; $i < strlen($line); $i++) {
      $countAll[$i][intval($line[$i])]++;
    }
  }
  return $countAll;
}

function removeLinesWith($binValue, $position, $lines)
{
  $res = [];
  if (count($lines) === 1) {
    return $lines;
  }
  foreach ($lines as $line) {
    if (intval($line[$position]) !== intval($binValue)) {
      array_push($res, $line);
    }
  }
  return $res;
}

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);
$bitCounts = getBitCounts($lines);

$binGamma = '';
$binEpsilon = '';
foreach ($bitCounts as $bitCount) {
  if ($bitCount[0] > $bitCount[1]) {
    $binGamma .= '0';
    $binEpsilon .= '1';
  } else {
    $binGamma .= '1';
    $binEpsilon .= '0';
  }
}

$part1 = bindec($binGamma) * bindec($binEpsilon);
p('Part 1: ' . $part1);

$oxygen = $lines;
$scrubber = $lines;
for ($i = 0; $i < strlen($lines[0]); $i++) {
  $oxygenBitCount = getBitCounts($oxygen);
  $oxygen = removeLinesWith($oxygenBitCount[$i][0] <= $oxygenBitCount[$i][1] ? 0 : 1, $i, $oxygen);

  $scrubberBitCount = getBitCounts($scrubber);
  $scrubber = removeLinesWith($scrubberBitCount[$i][0] <= $scrubberBitCount[$i][1] ? 1 : 0, $i, $scrubber);
}

$part2 = bindec($oxygen[0]) * bindec($scrubber[0]);
p('Part 2: ' . $part2);
