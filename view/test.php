<?php
    require_once('./autoload.php');

    use config\Database;
    use model\Users;

    $database = new Database("localhost", "userproject", "topu", "TopuRain004");
    $user = new Users(null, "");
