<?php ob_start();
require_once("functions.php"); ?>
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
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body{
            flex-direction: column;
            font-family: 'Quicksand', sans-serif;
        }
        form, .border{
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }
        .border{
            padding: 15px;
            border: 0.2px solid black;
        }
        .sub{
            padding: 10px;
            outline: none;
            border: 1px solid white;
            border-radius: 4px 3px 4px 3px;
            box-shadow: 2px 3px #e6e6e6;
        }
        .sub:hover{
            border: 1px solid #000000;
            background: #A5F8FF;
            padding: 13px;
            color: #000000;
            cursor: pointer;
        }
    </style>
</head>
<body>
        <h2>A basic Image Uploader for PHP</h2><br>
        <?php
        if((!empty($_POST["title"])) and (!empty($_POST["folder"])) and (!empty($_POST["password"])) and (!empty($_POST["size"])) and (!empty($_POST["redirect"]))){
            if(!is_numeric($_POST["size"])){
                header("Location: index.php?error=Please choose a correct size.");
                ob_end_flush(); die();
            }elseif(intval(filter($_POST["size"]) > 1000)){
                header("Location: index.php?error=Please choose a value under 1000MB.");
                ob_end_flush(); die();
            }elseif(strlen(filter($_POST["password"])) > 100){
                header("Location: index.php?error=Please choose a password under 100 character.");
                ob_end_flush(); die();
            }
            $patternForFolderName = "/(\"|\/|\\|\*|\:|\<|\>|\?|\|)+/mu";
            if(preg_match($patternForFolderName, filter($_POST["folder"]))){
                header("Location: index.php?error=Please use just allowed characters on folder name.");
                ob_end_flush(); die();
            }
            $folder = filter($_POST["folder"]) . "/";
            $database->set("settings", [
                "title" => filter($_POST["title"]),
                "size" => filter($_POST["size"]),
                "password" => filter($_POST["password"]),
                "folder" => filter($_POST["folder"]),
                "redirect" => filter($_POST["redirect"]),
            ], true);
            if(!file_exists($folder)){
                if(!mkdir($folder)){
                    ob_end_flush();
                    header("Location: index.php?error=Could not create the folder for images");
                    die();
                }
            }
            header("Location: index.php?success=Settings are saved with success.");
            ob_end_flush();
            die();
        }
        if(!$database->has("settings")){
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
                <div class="border">
                    <p>Welcome to the setup of image uploader!</p></br>
                    <p>Title of the site: </p><label>
                        <input type="text" name="title">
                    </label>
                    <p>Max size of you want to let images for upload (MB): </p><label>
                        <input type="number" name="size">
                    </label>
                    <p>Upload folder name: </p><label>
                        <input type="text" name="folder">
                    </label>
                    <p>The password for upload: </p><label>
                        <input type="text" name="password" required>
                    </label><br />
                    <span>Auto redirect to image link after the upload:</span>
                    <div style="display:flex; justify-content: normal; align-items: normal;">
                        <p>On</p><label for="on">
                            <input type="radio" id="on" name="redirect" value="on">
                        </label>
                        <p>Off</p><label for="off">
                            <input type="radio" id="off" name="redirect" value="off">
                        </label>
                    </div>
                    </br>
                    <div><input type="submit" value="Submit" class="sub"><br /></div>
                </div>
            </form>
            <br /><br />
        <?php
            ob_end_flush(); die();
        }
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
        <form action="result.php" method="post" enctype="multipart/form-data">
            <div class="border">
                <p>Select an image: </p><input type="file" name="img"></br>
                <div style="width: 100%"><p style="text-align: center;">OR</p></div>
                <p>Enter an image URL: </p><label>
                    <input type="url" name="url">
                </label>
                <p>Enter the password: </p><label>
                    <input type="password" name="pass" required>
                </label><br />
                <div><input type="submit" value="Submit" class="sub"><br /></div></br>
                <div style="width: 100%"><a href="images.php">All Pictures</a></div>
            </div>
        </form>
        <br /><br />
</body>
</html>
<?php ob_end_flush(); die();?>