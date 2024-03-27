<?php

//include_once('functions.php');

if (in_array('--help', $argv)) {
    //todo output the command line options

    return;
}

$params = getopt('', [
    'file:',
    'create_table::',
    'dry_run::',
    'u::',
    'p::',
    'h::',
]);

$file = $params['file'] ?? '';
if (empty($file)) {
    exit('Please include the csv file name using --file option'. PHP_EOL);
}

$data = getCsvData($file);







