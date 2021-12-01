<?php
require __DIR__.'/../utils.php';

$input = file_get_contents(__DIR__.'/input.txt');
$lines = input_to_lines($input);

$prev = PHP_INT_MAX;
$prevs = [PHP_INT_MAX,PHP_INT_MAX,PHP_INT_MAX];
$part1 = 0;
$part2 = 0;
foreach ($lines as $l) {
  $line = intval($l);
  if($line > $prev) {
    $part1++;
  }
  $sumprev = 0;
  $sumcurrent = 0;
  for($i = 0; $i<3; $i++) {
    $sumprev+= $prevs[$i];
    if($i > 0) {
      $sumcurrent+=$prevs[$i];
    }
  }
  $sumcurrent+=$line;
  if($sumcurrent > $sumprev) {
    $part2++;
  }

  $prev = $line;
  array_shift($prevs);
  array_push($prevs, $line);
}

p('Part 1: '.$part1);
p('Part 2: '.$part2);
