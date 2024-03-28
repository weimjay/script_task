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
    id INT(11) unsigned NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL DEFAULT '',
    surname VARCHAR(50) NOT NULL DEFAULT '',
    email VARCHAR(80) NOT NULL DEFAULT '',
    created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
EOF;
    try {
        $dbInstance = Database::getInstance($dsn, $dbUser, $dbPassword);
        $conn = $dbInstance->getConn();
        $res = $conn->exec($sql);
        exit('table users created successfully'.PHP_EOL);
    } catch (PDOException $e) {
        exit($e->getMessage());
    }
} elseif (isset($params['file'])) {
    $file = $params['file'];
    if (empty($file)) {
        exit('Please include the csv file name using --file option'. PHP_EOL);
    }
    $data = getCsvData($file);
    if (empty($data)) {
        exit('No data found');
    }
    array_shift($data);


}












