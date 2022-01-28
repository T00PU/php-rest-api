<?php
    spl_autoload_register(function($classname) {
        //pattern for checking config file or not
        $regex = "/config/";

        //find absolute dirname
        $path = dirname(__DIR__)."/".str_replace("\\", "/", $classname).".php";

        //check if it is from config file or not
        if(preg_match($regex, $classname)) $path = preg_replace("/(?<=\.)(php)/", "config.php", $path);
        require_once($path);
    });