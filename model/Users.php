<?php
    namespace model;

    use PDO;

    class Users {
        public string $name;
        public string $email;
        public $password;
        public int $userId;
        public string $projectName;
        public string $projectDescription;
        public string $status;

        private ?PDO $__connection = null;
        private string $__tableName = "";

        public function __construct($_dbcon, $_tableName) {
            $this->__connection = $_dbcon;
            $this->__tableName = $_tableName;
        }

        private function __sanitization() {
            $this->name = htmlentities(strip_tags($this->name));
            $this->email = htmlentities(strip_tags($this->email));
            $this->password = htmlentities(strip_tags($this->password));
        }

        public function create_user(): bool {
            $sqlCom = "INSERT INTO {$this->__tableName}(name, email, password) VALUES(:name, :email, :password)";
            $PDOdbObj = $this->__connection->prepare($sqlCom);

            //Hashing the password
            $this->password = password_hash($this->password, PASSWORD_ARGON2ID);

            $param = [":name" => $this->name, ":email" => $this->email, ":password" => $this->password];

            if($PDOdbObj->execute($param)) return true;

            return false;
        }

        public function pull_users_data(): array {
            return $this->check_email();
        }

        public function check_email(): array {
            $sqlCom = "SELECT * FROM {$this->__tableName} WHERE email = :email";
            $PDOdbObj = $this->__connection->prepare($sqlCom);
            //__sanitization
            $this->__sanitization();

            $param = [":email" => $this->email];

            if($PDOdbObj->execute($param)) return $PDOdbObj->fetch();

            return [];
        }

        public function check_login_user(): array {
            return $this->check_email();
        }

        public function create_project(): bool {
            $sqlCom = "INSERT INTO {$this->__tableName}(userId, projectName, status, description) VALUES(:userId, :projectName, :status, :description)";

            $PDOdbObj = $this->__connection->prepare($sqlCom);
            $this->__sanitization();

            $param = [":userId" => $this->userId, ":projectName" => $this->projectName, ":status" => $this->status, ":description" => $this->projectDescription];

            if($PDOdbObj->execute($param)) return true;

            return false;
        }
    }
