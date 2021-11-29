<?php
require 'utils.php';

if(count($argv) < 2) {
  p('Usage: php downloader.php <DAYNUMBER>');
  die();
}

$day = $argv[1];
if(!is_numeric($day)) {
  p('Usage: php downloader.php <DAYNUMBER>');
  die();
}


