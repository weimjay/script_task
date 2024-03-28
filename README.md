## script_task

#### Environment
- PHP 8.1.27
- MySQL 5.7.42


#### Command line options
- --file [csv file name] – this is the name of the CSV to be parsed
- --create_table – this will cause the MySQL users table to be built (and no further action will be taken)
- --dry_run – this will be used with the --file directive in case we want to run the script but not insert
into the DB. All other functions will be executed, but the database won't be altered
- -u – MySQL username
- -p – MySQL password
- -h – MySQL host
- --help – which will output the above list of directives with details.

#### Example running from the command line:
- php user_upload.php --help
