<?php
use Dotenv\Dotenv;

require 'bootstrap.php';

if(count($argv) < 2) {
  p('Usage: php downloader.php <DAYNUMBER>');
  die();
}

$day = $argv[1];
if(!is_numeric($day)) {
  p('Usage: php downloader.php <DAYNUMBER>');
  die();
}

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$advent_session = $_ENV['AOC_SESSION'];
if(!isset($advent_session)) {
  p('Check if your .env file exists and the session is set');
  die();
}

if(!file_exists('day'.$day)) {
  @mkdir('day'.$day);
  if (!file_exists('day'.$day)) {
    p('Could not create day directory, do it yourself or set permissions');
  }
}

if(!file_exists('day'.$day.'/input.txt')) {
  $url = 'https://adventofcode.com/2021/day/'.$day.'/input';
  // Create a stream
  $opts = array(
    'http'=>array(
      'method'=>"GET",
      'header'=>array("Accept-language: en","Cookie: session=".$advent_session)
    )
  );
  $context = stream_context_create($opts);
  $input = file_get_contents($url, false, $context);

  if(!$input || empty($input)) {
    p('Could not download input');
  } else {
    file_put_contents('day'.$day.'/input.txt', $input);
    if(!file_exists('day'.$day.'/run.php')) {
      copy('template.php', 'day'.$day.'/run.php');
    }
    p('Done, input downloaded');
  }
} else {
  p('Day already made');
}

