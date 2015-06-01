<?php
session_start();
require ('logininfo.php');

$mysqli = new mysqli($host, $user, $pw, $dbname);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL database <br>";
} 

$Userlogin = $_SESSION['Userlogin'];
/*
Code for storing image in database is obtained and modified from
http://www.onlinebuff.com/article_step-by-step-to-upload-an-image-and-store-in-database-using-php_40.html
*/

    function GetImageExtension($imagetype) {
        /*Checks image type*/
        if(empty($imagetype)) {
            return false;
        }
        switch($imagetype) {
            case 'image/bmp' : return '.bmp';
            case 'image/gif' : return '.gif';
            case 'image/jpeg' : return '.jpg';
            case 'image/png' : return '.png';
            default: return false;
        }
    }

    /*Assigns the image name*/
    if(!empty($_FILES["uploadedimage"]["name"])) {
        $file_name=$_FILES["uploadedimage"]["name"];
        $temp_name=$_FILES["uploadedimage"]["tmp_name"];
        $imgtype=$_FILES["uploadedimage"]["type"];
        $ext=GetImageExtension($imgtype);
        $imagename=$Userlogin.$ext;
        $target_path="uploads/" .$imagename;
    }

    /*INSERT query for image path and username into database*/
    if(move_uploaded_file($temp_name, $target_path)) {

        if(!$stmt = $mysqli->prepare("INSERT INTO video_game_pic (img_path, username) VALUES (?,?)")) {
            echo 'Cannot prepare image<br/><br/>';
        }
        if(!$stmt->bind_param('ss', $target_path, $Userlogin)) {
            echo 'Cannot bind parameters for image<br/><br/>';
        }
        if(!$stmt->execute()) {
            echo 'Cannot execute image<br/><br/>';
        }
    }
?>


<!DOCTYPE html>
<html>
    <!--http://webmaster.iu.edu/tools-and-guides/maintenance/redirect-meta-refresh.phtml-->
    <META http-equiv="refresh" content="0;URL=content.php">
</html>