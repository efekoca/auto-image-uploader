<?php
    if((basename($_SERVER["PHP_SELF"]) == basename(__FILE__)) or (basename($_SERVER["PHP_SELF"]) == substr(basename(__FILE__), 0, (strlen(basename(__FILE__)) - 4)))){
        header("Location: ./");
        die();
    }
    require("jsondb.php");
    $database = new jsonDB\db("data.json");
    try{
        $getDatabase = $database->get();
        if(empty($getDatabase["available"])){
            die("JSONDB error occurred.");
        }
        if(!empty($database->has("settings"))){
            $getSettings = $getDatabase["settings"];
            $src = "{$getSettings['folder']}/";
            $maxSize = $getSettings["size"];
            $password = $getSettings["password"];
            if(!file_exists($src)){
                if(!mkdir($src)){
                    header("Location: index.php?error=Could not create the folder for images");
                    die();
                }
            }
            if(empty($getSettings["size"])){
                $database->set("settings", [
                    "size" => "2"
                ], true);
            }
        }
    }catch(jsonDB\jsondbExpection $e){
        echo($e->errorMsg());
    }
    $website = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    function filter(String $param){
        $a = trim($param);
        $b = strip_tags($a);
        return htmlspecialchars($b, ENT_QUOTES);
    }
    function randomName(){
        return (md5(uniqid(mt_rand())));
    }
    function getFormattedTime($time){
        // date_default_timezone_set("Europe/Istanbul");
        return date("d/m/Y", $time) . ", " . date("H:i", $time);
    }
?>