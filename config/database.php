<?php

class Database {

    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=127.0.0.1;dbname=athena;charset=utf8";
        $user = "root";
        $password = "";

        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }


    public function getConnection() {
        return $this->pdo;
    }
}
