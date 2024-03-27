<?php

function getCsvData(string $file) : array {
    $row = 0;
    $result = [];
    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            for ($c = 0; $c < $num; $c ++) {
                $result[$row][$c] = $data[$c];
            }
            $row ++;
        }
        fclose($handle);
    }
    return $result;
}