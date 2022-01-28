<?php

    namespace config;
    use PDO;
    use PDOException;

    class Database {
        private string $_dsn;//data source name
        private ?PDO $_connection = null;

        // set some attribute
        private array $attr = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE => PDO::CASE_NATURAL, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

        public function __construct($_hostName, $_dbName, $_userName, $_passward) {
            //create data source name
            $this->_dsn = "mysql:host={$_hostName};dbname={$_dbName}";

            try {
                //create PDO connection object instances
                $this->_connection = new PDO($this->_dsn, $_userName, $_passward, $this->attr);

            } catch (PDOException $error) {
                echo "Connection Error: {$error->getCode()}";
                //echo "</br>Connection Error: {$error->getMessage()}";

                // echo "Connection Error: {$error->getCode()}";
                // echo "Connection Error: {$error->getCode()}";
            }

        }

        public function connection() {
            if($this->_connection) return $this->_connection;

            return false;
        }
    }
