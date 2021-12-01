<?php
function p($txt) {
  echo $txt."\n";
}

function input_to_lines($input) {
  return array_map("trim", explode("\n", trim($input)));
}