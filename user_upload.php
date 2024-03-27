<?php

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

$filename = $params['file'] ?? '';
if (empty($filename)) {
    exit('Please include the csv file name using --file option'. PHP_EOL);
}


