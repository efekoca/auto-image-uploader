<?php
    require_once("functions.php");
    if(!$database->has("settings")){
        header("Location: index.php");
        die();
    }
    if(empty($_POST["pass"])){
        header("Location: index.php");
        die();
    }elseif(filter($_POST["pass"]) !== $password){
        header("Location: index.php?error=Wrong password");
        die();
    }
    $img = $_FILES["img"];
    $randImgName = randomName();
    $formattedSize = (intval($getSettings["size"]) * 1024);
    if($getSettings["redirect"] == "on"){
        if((empty($_POST["url"])) and ($img["error"] < 1)){
            $errorCount = 0;
            $size = round($img["size"] / 1024);
            if($size > $formattedSize){
                header("Location: index.php?error=Please choose a file with size under {$formattedSize} KB.");
                die();
            }
            switch($img["name"]){
                case(substr($img["name"], -4) == "jpeg") or (substr($img["name"], -4) == "webp"):
                    $formattedImgName = $src . $randImgName . "." . substr($img["name"], -4);
                    break;
                default:
                    $formattedImgName = $src . $randImgName . substr($img["name"], -4);
            }
            if(move_uploaded_file($img["tmp_name"], $formattedImgName)){
                header("Location: $formattedImgName");
                die();
            }else{
                header("Location: index.php?error=An unexpected error occurred while uploading the file.");
                die();
            }
        }elseif((!empty($_POST["url"])) and ($img["error"] > 0)){
            $url = filter($_POST["url"]);
            $data = file_get_contents($url);
            switch($url){
                case(substr($url, -4) == "jpeg") or (substr($url, -4) == "webp"):
                    $formattedImgName = $src . $randImgName . "." . substr($url, -4);
                    break;
                default:
                    $formattedImgName = $src . $randImgName . substr($url, -4);
            }
            if(file_put_contents($formattedImgName, $data)){
                $size = round(filesize($formattedImgName) / 1024);
                if($size > $formattedSize){
                    unlink($formattedImgName);
                    header("Location: index.php?error=Please enter an url with size under {$formattedSize} KB.");
                    die();
                }
                header("Location: $formattedImgName");
                die();
            }else{
                header("Location: index.php?error=An unexpected error occurred.");
                die();
            }
        }
    }
    if((!empty($_POST["url"])) and ($img["error"] < 1)){
        $url = filter($_POST["url"]);
        $dataForUrl = file_get_contents($url);
        switch($url){
            case(substr($url, -4) == "jpeg") or (substr($url, -4) == "webp"):
                $formattedVisibleExtensionForUrl = substr($url, -4);
                $formattedVisibleNameForUrl = substr($url, 0, (strlen($url) - 5));
                $formattedImgNameForUrl = $src . $randImgName . "." . substr($url, -4);
                break;
            default:
                $formattedVisibleExtensionForUrl = substr($url, -3);
                $formattedVisibleNameForUrl = substr($url, 0, (strlen($url) - 4));
                $formattedImgNameForUrl = $src . $randImgName . substr($url, -4);
        }
        $patternForUrl = "//u";
        $splittedForUrl = preg_split($patternForUrl, $formattedVisibleNameForUrl);
        $finalStringForUrl = "";
        $iForUrl = 0;
        foreach($splittedForUrl as $x){
            if($iForUrl == 0){
                $finalStringForUrl .= strtoupper($x);
            }else{
                $finalStringForUrl .= strtolower($x);
            }
            $iForUrl++;
        }
        if(file_put_contents($formattedImgNameForUrl, $dataForUrl)){
            $sizeForUrl = round(filesize($formattedImgNameForUrl) / 1024);
            if($sizeForUrl > $formattedSize){
                unlink($formattedImgNameForUrl);
                header("Location: index.php?error=Please enter an url with size under {$formattedSize} KB.");
                die();
            } ?>
            <!DOCTYPE HTML>
            <html lang="en">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta http-equiv="Content-Language" content="en">
                <meta charset="utf-8">
                <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600&display=swap" rel="stylesheet">
                <link rel="shortcut icon" type="image/png" href="https://cdn0.iconfinder.com/data/icons/set-app-incredibles/24/Image-01-512.png"/>
                <meta name="viewport" content="width=device-width,initial-scale=1"/>
                <meta name="theme-color" content="#665437"/>
                <meta name="description" content="A basic image uploader example for PHP"/>
                <title><?php echo(!empty($database->has("settings")) ? $getSettings["title"] : "Image Uploader"); ?></title>
                <style>
                    html, body{
                        margin: 0 auto;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    body{
                        flex-direction: column;
                        font-family: 'Quicksand', sans-serif;
                    }
                </style>
            </head>
            <body>
            <div style='display: flex; justify-content: center; align-items: center; flex-direction:column;'>
                <span><strong>The image uploaded with success!</strong></span></br>
                <br /> Image name: <?php echo($finalStringForUrl); ?>
                <br /> Image extension: <?php echo($formattedVisibleExtensionForUrl); ?>
                <br /> Image size: <?php echo($sizeForUrl); ?> KB
                <br />Image link: <a href="<?php echo($formattedImgNameForUrl); ?>" target="_blank">Click!</a>
                <br /> Image: <br />
                <center><img width='100' height='100' src='<?php echo($formattedImgNameForUrl); ?>' alt='<?php echo($finalStringForUrl); ?>'></center>
            </div></br>
        <?php }else{
            header("Location: index.php?error=An unexpected error occurred while uploading on URL.");
            die();
        }
        $errorCount = 0;
        $size = round($img["size"] / 1024);
        $formattedSize = (intval($getSettings["size"]) * 1024);
        if($size > $formattedSize){
            header("Location: index.php?error=Please choose a file with size under {$formattedSize} KB.");
            die();
        }
        switch($img["name"]){
            case(substr($img["name"], -4) == "jpeg") or (substr($img["name"], -4) == "webp"):
                $formattedVisibleExtension = substr($img["name"], -4);
                $formattedVisibleName = substr($img["name"], 0, (strlen($img["name"]) - 5));
                $formattedImgName = $src . $randImgName . "." . substr($img["name"], -4);
                break;
            default:
                $formattedVisibleExtension = substr($img["name"], -3);
                $formattedVisibleName = substr($img["name"], 0, (strlen($img["name"]) - 4));
                $formattedImgName = $src . $randImgName . substr($img["name"], -4);
        }
        $pattern = "//u";
        $splitted = preg_split($pattern, $formattedVisibleName);
        $finalString = "";
        $i = 0;
        foreach($splitted as $x){
            if($i == 0){
                $finalString .= strtoupper($x);
            }else{
                $finalString .= strtolower($x);
            }
            $i++;
        }
        if(move_uploaded_file($img["tmp_name"], $formattedImgName)){ ?>
            <!DOCTYPE HTML>
            <html lang="en">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta http-equiv="Content-Language" content="en">
                <meta charset="utf-8">
                <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600&display=swap" rel="stylesheet">
                <link rel="shortcut icon" type="image/png" href="https://cdn0.iconfinder.com/data/icons/set-app-incredibles/24/Image-01-512.png"/>
                <meta name="viewport" content="width=device-width,initial-scale=1"/>
                <meta name="theme-color" content="#665437"/>
                <meta name="description" content="A basic image uploader example for PHP"/>
                <title><?php echo(!empty($database->has("settings")) ? $getSettings["title"] : "Image Uploader"); ?></title>
                <style>
                    html, body{
                        margin: 0 auto;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    body{
                        flex-direction: column;
                        font-family: 'Quicksand', sans-serif;
                    }
                </style>
            </head>
            <body>
            <div style='display: flex; justify-content: center; align-items: center; flex-direction:column;'>
                <span><strong>The image uploaded with success!</strong></span></br>
                <br /> Image full name: <?php echo($img["name"]); ?>
                <br /> Image name: <?php echo($finalString); ?>
                <br /> Image extension: <?php echo($formattedVisibleExtension); ?>
                <br />Image type: <?php echo($img["type"]); ?>
                <br /> Image size: <?php echo($size); ?> KB
                <br />Image link: <a href="<?php echo($formattedImgName); ?>" target="_blank">Click!</a>
                <br /> Image: <br />
                <center><img width='100' height='100' src='<?php echo($formattedImgName); ?>' alt='<?php echo($finalString); ?>'></center>
            </div></br>
        <?php }else{
            $errorCount += 1;
        }
        if($errorCount > 0){
            $getImageCount = count($img["name"]) - $errorCount;
            header("Location: index.php?error=An unexpected error occurred while uploading the file.");
            die();
        }
    }elseif((!empty($_POST["url"])) and ($img["error"] > 0)){
        $url = filter($_POST["url"]);
        $data = file_get_contents($url);
        switch($url){
            case(substr($url, -4) == "jpeg") or (substr($url, -4) == "webp"):
                $formattedVisibleExtension = substr($url, -4);
                $formattedVisibleName = substr($url, 0, (strlen($url) - 5));
                $formattedImgName = $src . $randImgName . "." . substr($url, -4);
                break;
            default:
                $formattedVisibleExtension = substr($url, -3);
                $formattedVisibleName = substr($url, 0, (strlen($url) - 4));
                $formattedImgName = $src . $randImgName . substr($url, -4);
        }
        $pattern = "//u";
        $splitted = preg_split($pattern, $formattedVisibleName);
        $finalString = "";
        $i = 0;
        foreach($splitted as $x){
            if($i == 0){
                $finalString .= strtoupper($x);
            }else{
                $finalString .= strtolower($x);
            }
            $i++;
        }
        if(file_put_contents($formattedImgName, $data)){
            $size = round(filesize($formattedImgName) / 1024);
            if($size > $formattedSize){
                unlink($formattedImgName);
                header("Location: index.php?error=Please enter an url with size under {$formattedSize} KB.");
                die();
            } ?>
            <!DOCTYPE HTML>
            <html lang="en">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta http-equiv="Content-Language" content="en">
                <meta charset="utf-8">
                <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600&display=swap" rel="stylesheet">
                <link rel="shortcut icon" type="image/png" href="https://cdn0.iconfinder.com/data/icons/set-app-incredibles/24/Image-01-512.png"/>
                <meta name="viewport" content="width=device-width,initial-scale=1"/>
                <meta name="theme-color" content="#665437"/>
                <meta name="description" content="A basic image uploader example for PHP"/>
                <title><?php echo(!empty($database->has("settings")) ? $getSettings["title"] : "Image Uploader"); ?></title>
                <style>
                    html, body{
                        margin: 0 auto;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    body{
                        flex-direction: column;
                        font-family: 'Quicksand', sans-serif;
                    }
                </style>
            </head>
            <body>
            <div style='display: flex; justify-content: center; align-items: center; flex-direction:column;'>
                <span><strong>The image uploaded with success!</strong></span></br>
                <br /> Image name: <?php echo($finalString); ?>
                <br /> Image extension: <?php echo($formattedVisibleExtension); ?>
                <br /> Image size: <?php echo($size); ?> KB
                <br />Image link: <a href="<?php echo($formattedImgName); ?>" target="_blank">Click!</a>
                <br /> Image: <br />
                <center><img width='100' height='100' src='<?php echo($formattedImgName); ?>' alt='<?php echo($finalString); ?>'></center>
            </div></br>
        <?php }else{
            header("Location: index.php?error=An unexpected error occurred.");
            die();
        }
    }elseif((empty($_POST["url"])) and ($img["error"] < 1)){
        $errorCount = 0;
            $size = round($img["size"] / 1024);
            $formattedSize = (intval($getSettings["size"]) * 1024);
            if($size > $formattedSize){
                header("Location: index.php?error=Please choose a file with size under {$formattedSize} KB.");
                die();
            }
            switch($img["name"]){
                case(substr($img["name"], -4) == "jpeg") or (substr($img["name"], -4) == "webp"):
                    $formattedVisibleExtension = substr($img["name"], -4);
                    $formattedVisibleName = substr($img["name"], 0, (strlen($img["name"]) - 5));
                    $formattedImgName = $src . $randImgName . "." . substr($img["name"], -4);
                    break;
                default:
                    $formattedVisibleExtension = substr($img["name"], -3);
                    $formattedVisibleName = substr($img["name"], 0, (strlen($img["name"]) - 4));
                    $formattedImgName = $src . $randImgName . substr($img["name"], -4);
            }
            $pattern = "//u";
            $splitted = preg_split($pattern, $formattedVisibleName);
            $finalString = "";
            $i = 0;
            foreach($splitted as $x){
                if($i == 0){
                    $finalString .= strtoupper($x);
                }else{
                    $finalString .= strtolower($x);
                }
                $i++;
            }
            if(move_uploaded_file($img["tmp_name"], $formattedImgName)){ ?>
                <!DOCTYPE HTML>
                <html lang="en">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <meta http-equiv="Content-Language" content="en">
                    <meta charset="utf-8">
                    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600&display=swap" rel="stylesheet">
                    <link rel="shortcut icon" type="image/png" href="https://cdn0.iconfinder.com/data/icons/set-app-incredibles/24/Image-01-512.png"/>
                    <meta name="viewport" content="width=device-width,initial-scale=1"/>
                    <meta name="theme-color" content="#665437"/>
                    <meta name="description" content="A basic image uploader example for PHP"/>
                    <title><?php echo(!empty($database->has("settings")) ? $getSettings["title"] : "Image Uploader"); ?></title>
                    <style>
                        html, body{
                            margin: 0 auto;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        body{
                            flex-direction: column;
                            font-family: 'Quicksand', sans-serif;
                        }
                    </style>
                </head>
                <body>
                <div style='display: flex; justify-content: center; align-items: center; flex-direction:column;'>
                    <span><strong>The image uploaded with success!</strong></span></br>
                    <br /> Image full name: <?php echo($img["name"]); ?>
                        <br /> Image name: <?php echo($finalString); ?>
                        <br /> Image extension: <?php echo($formattedVisibleExtension); ?>
                        <br />Image type: <?php echo($img["type"]); ?>
                        <br /> Image size: <?php echo($size); ?> KB
                        <br />Image link: <a href="<?php echo($formattedImgName); ?>" target="_blank">Click!</a>
                        <br /> Image: <br />
                        <center><img width='100' height='100' src='<?php echo($formattedImgName); ?>' alt='<?php echo($finalString); ?>'></center>
                </div></br>
            <?php }else{
                $errorCount += 1;
            }
        if($errorCount > 0){
            $getImageCount = count($img["name"]) - $errorCount;
            header("Location: index.php?error=An unexpected error occurred while uploading the file.");
            die();
        }
    }else{
        header("Location: index.php?error=An unexpected error occurred.");
        die();
    }
?>
</body>
</html>
<?php die(); ?>