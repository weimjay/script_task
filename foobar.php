<?php

$data = [];
for ($i = 1; $i <= 100; $i ++) {
    $element = $i;
    $divisible3 = $i % 3 == 0;
    $divisible5 = $i % 5 == 0;
    if ($divisible3 && $divisible5) {
        $element = 'foobar';
    } elseif ($divisible3) {
        $element = 'foo';
    } elseif ($divisible5) {
        $element = 'bar';
    }
    $data[] = $element;
}
$output = implode(', ', $data);
echo $output. PHP_EOL;