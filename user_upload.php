<?php

include_once('database.class.php');
include_once('functions.php');

$params = getopt('u:p:h:', [
    'file:',
    'create_table::',
    'dry_run::',
    'help::',
]);

if (isset($params['help'])) {
    $help = <<<EOF
These are command line options:

• --file [csv file name] – this is the name of the CSV to be parsed
• --create_table – this will cause the MySQL users table to be built (and no further action will be taken)
• --dry_run – this will be used with the --file directive in case we want to run the script but not insert
into the DB. All other functions will be executed, but the database won't be altered
• -u – MySQL username
• -p – MySQL password
• -h – MySQL host
• --help – which will output the above list of directives with details.

EOF;
    exit($help);
} elseif (isset($params['create_table'])) {
    $host = $params['h'] ?? '';
    $dbUser = $params['u'] ?? '';
    $dbPassword = $params['p'] ?? '';
    $dsn = "mysql:host=$host;dbname=db;charset=utf8";
    $sql = <<<EOF
CREATE TABLE IF NOT EXISTS users (
    `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL DEFAULT '',
    `surname` VARCHAR(50) NOT NULL DEFAULT '',
    `email` VARCHAR(80) NOT NULL DEFAULT '',
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
EOF;
    try {
        $dbInstance = Database::getInstance($dsn, $dbUser, $dbPassword);
        $conn = $dbInstance->getConn();
        $res = $conn->exec($sql);
        exit('[INFO] table users created successfully'. PHP_EOL);
    } catch (PDOException $e) {
        exit($e->getMessage(). PHP_EOL);
    }
} elseif (isset($params['file'])) {
    $file = $params['file'];
    if (empty($file)) {
        exit('[Error] Please include the csv file name using --file option'. PHP_EOL);
    }
    $data = getCsvData($file);
    if (empty($data)) {
        exit('[Error] No data found'. PHP_EOL);
    }
    $fields = array_shift($data);
    $fieldMap = array_flip(array_map('trim', $fields));
    $insertData = [];
    $emailMap = [];
    foreach ($data as $k => $arr) {
        $row = [];
        $flag = true;
        $emailIdx = $fieldMap['email'];
        $email = trim($arr[$emailIdx]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo sprintf("[INFO] email is invalid, value: %s, row: %d".PHP_EOL, $email, $k);
            continue;
        }
        if (isset($emailMap[$email])) {
            echo sprintf("[INFO] email exists, skip this row, value: %s, row: %d".PHP_EOL, $email, $k);
            continue;
        } else {
            $emailMap[$email] = true;
        }
        foreach ($arr as $i => $item) {
            $field = trim($fields[$i]);
            $value = trim($item);
            if ($field == 'email') {
                $row[$field] = strtolower($value);
            } else {
                $row[$field] = ucfirst($value);
            }
        }
        $flag && $insertData[$k] = $row;
    }
    if (empty($insertData)) {
        exit('[Error] no valid data to insert'.PHP_EOL);
    }
    if (isset($params['dry_run'])) {
        echo '[INFO] The data to be inserted: '.PHP_EOL;
        foreach ($insertData as $k => $val) {
            echo sprintf("row: %d, name: %s, surname: %s, email: %s", $k, $val['name'], $val['surname'], $val['email']).PHP_EOL;
        }
        exit;
    }
    $host = $params['h'] ?? '';
    $dbUser = $params['u'] ?? '';
    $dbPassword = $params['p'] ?? '';
    $dsn = "mysql:host=$host;dbname=db;charset=utf8";
    $dbInstance = Database::getInstance($dsn, $dbUser, $dbPassword);
    $res = $dbInstance->batchInsert('users', $insertData);
    if (!$res) {
        exit($dbInstance->errorMsg().PHP_EOL);
    }
    exit(sprintf("[INFO] Data insert to database complete, success rows: %d".PHP_EOL, count($insertData)));
}












