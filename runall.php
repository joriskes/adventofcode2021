<?php
$torun = [];
for ($i = 1; $i <= 25; $i++) {
    if (file_exists(__DIR__ . '/day' . $i . '/run.php')) {
        $torun[] = [
            'file' => __DIR__ . '/day' . $i . '/run.php',
            'title' => 'Day ' . $i
        ];
    }
}

$executionStartTime = microtime(true);
foreach ($torun as $running) {
    echo $running['title'] . "\n";
    $dayStartTime = microtime(true);
    include $running['file'];
    $dayTime = number_format((microtime(true) - $dayStartTime) * 1000, 3, '.', '') . 'ms';
    echo 'Day time ' . $dayTime . "\n\n";
}
$executionTime = number_format((microtime(true) - $executionStartTime) * 1000, 3, '.', '') . 'ms';
echo 'Total time: ' . $executionTime . "\n";
