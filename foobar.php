<?php

$data = [];
for ($i = 1; $i <= 100; $i ++) {
    $element = $i;
    if ($i % 3 == 0 && $i % 5 == 0) {
        $element = 'foobar';
    } elseif ($i % 3 == 0) {
        $element = 'foo';
    } elseif ($i % 5 == 0) {
        $element = 'bar';
    }
    $data[] = $element;
}
$output = implode(', ', $data);
echo $output. PHP_EOL;