<?php
    //Define require headers
    header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    // header("Access-Control-Expose-Headers: X-Topu-Croxo");
    // header("X-Topu-Croxo: croxo");
    header("Cache-Control: no-cache");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Origin, Access-Control-Allow-Credentials, Access-Control-Allow-Methods, Content-Type, Access-Control-Expose-Headers");

   function set_cookie_new($Name, $Value = '', $Expires = 0, $Path = '', $Domain = '', $Secure = false, $HTTPOnly = false) {
       $date = date("D, d M Y H:i:s", $Expires) . ' GMT';
       header("Set-Cookie: " . rawurlencode($Name) . "=" . rawurlencode($Value)
                            ."; Expires={$date}"."; Max-Age=".($Expires - time())
                            ."; Path={$Path}; Domain={$Domain}".(!$Secure?"": "; Secure"
                            .(!$HTTPOnly?"":"; HttpOnly")), false);
       // header('Set-Cookie: ' . rawurlencode($Name) . '=' . rawurlencode($Value)
       //                  .(empty($expires)? "" : '; Expires='.$date)
       //                  . (empty($MaxAge) ? '' : '; Max-Age='.($expires - time()))
       //                  . (empty($Path)   ? '' : '; path=' . $Path)
       //                  . (empty($Domain) ? '' : '; domain=' . $Domain)
       //                  . (!$Secure       ? '' : '; secure')
       //                  . (!$HTTPOnly     ? '' : '; HttpOnly'), false);
}

    //require config and model file
    require_once("../config/Database.config.php");
    require_once("../model/Users.php");

    use config\Database;
    use model\Users;

    //create database connection
    $database = new Database("localhost", "userproject", "topu", "TopuRain004");
    $dbcon = $database->connection();

    //create model object
    $user = new Users($dbcon, "users");

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $postedData = json_decode(file_get_contents("php://input"));
        if(!empty($postedData->username) && !empty($postedData->email) && !empty($postedData->password)) {
            $user->name = $postedData->username;
            $user->email = $postedData->email;
            $user->password = $postedData->password;

            if(!empty($user->check_email())) {
                //email already exits
                http_response_code(409);
                echo json_encode([
                    "status" => 0,
                    "message" => "User already exist:(.Please try with another email."
                ]);
            }else {
                if($user->create_user()) {
                    $userData = $user->pull_users_data();
                    //starting session
                    session_start();
                    $_SESSION["_uxd"] = $userData["id"];
                    $_SESSION["_user"] = $userData["name"];

                    // set_cookie_new("username", $user->name, time()+(60*60), "/", "localhost", false, false);
                    setcookie("_uxd", $userData["id"], time() + 60, "/", "localhost", false, false);
                    
                    http_response_code(200);
                    echo json_encode([
                        "status" => 1,
                        "message" => "Successfully created user:)"
                    ]);
                }else {
                    http_response_code(500);
                    echo json_encode([
                        "status" => 0,
                        "message" => "Something went wrong:<"
                    ]);
                }
            }
        }else {
            http_response_code(406);
            echo json_encode([
                "status" => 0,
                "message" => "Fill up all data correctly"
            ]);
        }
    }else {
        http_response_code(500);//405 method not allowed
        echo json_encode([
            "status" => 0,
            "message" => "Access denied:("
        ]);
    }
