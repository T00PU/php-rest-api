<?php
    //Define require headers
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Content-Type");

    //require autoload.php
    require_once("../vendor/autoload.php");

    use Firebase\JWT\JWT;

    //require config and model file
    require_once("../config/Database.config.php");
    require_once("../model/Users.php");

    use config\Database;
    use model\Users;

    //create database connection
    $database = new Database("localhost", "userproject", "topu", "TopuRain004");
    $dbcon = $database->connection();

    //create model object
    $user = new Users($dbcon, "projects");

    function decode($encodedToken) {
        $key = "somthin....74$%#@@";
        return JWT::decode($encodedToken, $key, ["HS512"]);
    }

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $postedData = json_decode(file_get_contents("php://input"));
        $getheaders = getallheaders();

        if(!empty($postedData->projectname) && !empty($postedData->projectstatus) && !empty($postedData->projectdescription)) {
            try {
                //$decodedToken = decode($getheaders["Authorization"]);
                $user->userId = $_COOKIE["id"];//$decodedToken->_uds->id;
                $user->projectName = $postedData->projectname;
                $user->projectDescription = $postedData->projectdescription;
                $user->status = $postedData->projectstatus;

                if($user->create_project()) {
                    http_response_code(200);
                    echo json_encode([
                        "status" => 1,
                        "msg" => "Project has been successfully created:)",
                        "username" => $_COOKIE["username"]
                    ]);
                }else {
                    http_response_code(500);
                    echo json_encode([
                        "status" => 0,
                        "msg" => "Something went worn:<"
                    ]);
                }
            } catch (Exception $exp) {
                http_response_code(500);
                echo json_encode([
                    "status" => 0,
                    "msg" => $exp->getMessage()
                ]);
            }
        }else {
            http_response_code(403);
            echo json_encode([
                "status" => 0,
                "msg" => "Fill up data correctly"
            ]);
        }
    }else {
        http_response_code(503);

        echo json_encode([
            "status" => 0,
            "msg" => "Access denied:("
        ]);
    }
