<?php

    //Define require headers
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Content-Type");

    //require autoload.php
    require_once("../vendor/autoload.php");

    use Firebase\JWT\JWT;
    use Dotenv\Dotenv;

    //require config and model file
    echo require_once("../config/Database.config.php");
    exit;
    require_once("../model/Users.php");

    use config\Database;
    use model\Users;

    //create database connection
    $database = new Database("localhost", "userproject", "topu", "TopuRain004");
    $dbcon = $database->connection();

    //create model object
    $user = new Users($dbcon, "users");

    //loading env
    $dotenv = Dotenv::createImmutable("../");
    $dotenv->load();

    function generate_token($data): string {
        $userId = $data["id"];
        $name = $data["name"];
        $email = $data["email"];
        $key = getenv("S_KEY");

        $iss = "croxo";
        $iat = time();
        $nbf = $iat + 5;
        $exp = $iat + 180;
        $aud = hash("SHA256", "n_users", false);
        $_uds = [
            "id" => $userId,
            "name" => $name,
            "email" => $email
        ];

        $payload = [
            "iss" => $iss,
            "iat" => $iat,
            "nbf" => $nbf,
            "exp" => $exp,
            "aud" => $aud,
            "_uds" => $_uds
        ];

        return JWT::encode($payload, $key, "HS512");
    }

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $postedData = json_decode(file_get_contents("php://input"));

        if(!empty($postedData->email) && !empty($postedData->password)) {
            $user->email = $postedData->email;
            $getData = $user->check_login_user();

            if(!empty($getData)) {
                //check only password if exist in db
                $password = $getData["password"];

                //checking password if match
                if(password_verify($postedData->password, $password)) {
                    //generate token goes on .............
                    $encodedToken = generate_token($getData);

                    http_response_code(200);
                    echo json_encode([
                        "status" => 1,
                        "token" => $encodedToken,
                        "message" => "Logged in successfully:)"
                    ]);
                }else {
                    http_response_code(404);
                    echo json_encode([
                        "status" => 0,
                        "message" => "Wrong email or password:>"
                    ]);
                }

            }else {
                http_response_code(404);
                echo json_encode([
                    "status" => 0,
                    "message" => "Wrong email or password:>"
                ]);
            }
        }else {
            http_response_code(404);
            echo json_encode([
                "status" => 0,
                "message" => "All credential data needed."
            ]);
        }

    }else {
        http_response_code(503);
        echo json_encode([
            "status" => 0,
            "message" => "Access denied:("
        ]);
    }
