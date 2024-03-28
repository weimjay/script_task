<?php

class Database {
    private static $instance = null;
    private $pdo;
    private $error;

    private function __construct($dsn, $username, $password) {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $this->pdo = new PDO($dsn, $username, $password, $options);
    }

    public static function getInstance($dsn, $username, $password) {
        if (!self::$instance) {
            self::$instance = new Database($dsn, $username, $password);
        }
        return self::$instance;
    }

    public function getConn() {
        return $this->pdo;
    }

    private function __clone() {}

    public function create($table, $data) {
        try {
            $columns = implode(', ', array_keys($data));
            $values = implode(', :', array_keys($data));
            $sql = "INSERT INTO $table ($columns) VALUES (:$values)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function update($table, $where, $data) {
        try {

            $set = '';
            foreach ($data as $key => $value) {
                $set .= "$key = :$key, ";
            }
            $set = rtrim($set, ', ');

            $whereClause = '';
            foreach ($where as $key => $value) {
                $whereClause .= "$key = :where_$key AND ";
            }
            $whereClause = rtrim($whereClause, 'AND ');

            $query = "UPDATE $table SET $set WHERE $whereClause";

            $stmt = $this->pdo->prepare($query);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            foreach ($where as $key => $value) {
                $stmt->bindValue(":where_$key", $value);
            }

            $stmt->execute();

            return true;

        } catch (PDOException $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    public function select($table, $fields = "*", $conditions = null, $likeConditions = null, $orderBy = null, $limit = null, $params = array()) {
        try {
            if (is_array($fields)) {
                $fields = implode(', ', $fields);
            } elseif ($fields === "*") {
                $fields = "*";
            } else {
                $fields = "";
            }

            $whereClause = '';
            if (!is_null($conditions) && is_array($conditions)) {
                foreach ($conditions as $key => $value) {
                    $whereClause .= "$key = :$key AND ";
                }
                $whereClause = rtrim($whereClause, 'AND ');
            }

            if (!is_null($likeConditions) && is_array($likeConditions)) {
                if (!empty($whereClause)) {
                    $whereClause .= ' AND ';
                }
                foreach ($likeConditions as $key => $value) {
                    $whereClause .= "$key LIKE :like_$key AND ";
                    $params[":like_$key"] = $value;
                }
                $whereClause = rtrim($whereClause, 'AND ');
            }

            $orderByClause = '';
            if (!is_null($orderBy) && is_array($orderBy)) {
                $orderByClause = "ORDER BY " . implode(', ', $orderBy);
            }

            $limitClause = '';
            if (!is_null($limit)) {
                $limitClause = "LIMIT $limit";
            }

            $query = "SELECT $fields FROM $table";
            if (!empty($whereClause)) {
                $query .= " WHERE $whereClause";
            }
            if (!empty($orderByClause)) {
                $query .= " $orderByClause";
            }
            if (!empty($limitClause)) {
                $query .= " $limitClause";
            }

            $stmt = $this->pdo->prepare($query);

            if (!is_null($conditions) && is_array($conditions)) {
                foreach ($conditions as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }
            }

            if (!is_null($likeConditions) && is_array($likeConditions)) {
                foreach ($likeConditions as $key => $value) {
                    $stmt->bindValue(":like_$key", $value);
                }
            }

            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function errorMsg() {
        return $this->error;
    }
}