<?php
    require_once("functions.php");
    if(!$database->has("settings")){
        header("Location: index.php");
        die();
    }
?>
<!doctype html>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body{
            flex-direction: column;
            font-family: 'Quicksand', sans-serif;
        }
        *{
            box-sizing: border-box;
        }
        .column{
            float: left;
            padding: 5px;
        }
        .row::after{
            content: "";
            clear: both;
            display: table;
        }
        @media screen and(max-width: 500px){
            .column{
                width: 100%;
            }
        }
    </style>
</head>
<body>
<h2>The images you uploaded</h2><br>
<?php
    if(!empty($_GET["error"])){ ?>
        <div>
            <p>
                <?php echo(filter($_GET["error"])); ?>
            </p>
        </div>
    <?php }elseif(!empty($_GET["success"])){ ?>
        <div>
            <p>
                <?php echo(filter($_GET["success"])); ?>
            </p>
        </div></br>
    <?php } ?>
    <form method="post" enctype="multipart/form-data">
                <div class="row" style="align-items: center; justify-content: center">
                    <?php
                    if($handle = opendir($src)){
                        while(false !== ($entry = readdir($handle))){
                            if($entry != "." && $entry != ".."){ ?>
                                <div class="column">
                                    <a href="<?php echo($src . $entry); ?>" target="_blank"><img src="<?php echo($src . $entry); ?>" alt="" style="width:200px; height: 200px;"></a>
                                </div>
                            <?php }
                        }
                        closedir($handle);
                    } ?>
                </div>
    </form>
</body>
</html>