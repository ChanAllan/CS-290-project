<?php
session_start();

require ('logininfo.php');

$Userlogin = $_SESSION['Userlogin'];

$mysqli = new mysqli($host, $user, $pw, $dbname);
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL database <br>";
} 

  if(isset($_GET['action']) && $_GET['action'] == 'logout') {

  /*Sets session to an empty array*/
  $_SESSION = array();
  /*Destroys the session id*/
  session_destroy();

  /*Redirects to newlogin.php after ending session*/
  $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
  $filePath = implode('/',$filePath);
  $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;  
  header("Location: newindex.html", true);
  die();
  }

if(!isset($_SESSION['Userlogin']) || $_SESSION['Userlogin'] == '') {
	header( 'Location: newindex.html' ); /*Redirect to login page if not logged in*/ 
	echo "The session login is not set";
} else if (isset($_SESSION['Userlogin'])) {
	echo "<h1>Welcome To Your Game Library Database " .$Userlogin. "!</<h1>";

	/*Attempted to display picture from database for testing purposes, but did not function correctly.
	$pic = $mysqli->prepare("SELECT img_path FROM video_game_pic WHERE username=\"$Userlogin\"");
	$pic->execute();
	$result = $pic->get_result();
	$display = $result->fetch_assoc();
	echo "<img src=\"$display[img_path]\">"
	*/
} 


?>



<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">		 
		<link href="style.css" rel="stylesheet" type="text/css">
    	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lato">
		
		<title>Games List Member Page</title>
	</head>
<body>
	<fieldset>
		<div class="form_input">
		<form name="add_form" method="post">
			<label>Video Game Name: </label>
			<input type="text" name="video_game_name"/><br/>
			<label>Video Game Category: </label>
			<input type="text" name="video_game_cat"/><br/>
			<label>Video Game Rating: </label>
			<select name="rating">
				<option value="1">One Star (horrible)</option>
				<option value="2">Two Stars</option>
				<option value="3">Three Stars</option>
				<option value="4">Four Stars</option>
				<option value="5">Five Stars (Excellent)</option>
			</select><br/>
			<p><input type="submit" value="Add Video Game" name="add_video_game" /></p>
		</form>
		</div>
	</fieldset>

<!--Referred to 
	http://www.onlinebuff.com/article_step-by-step-to-upload-an-image-and-store-in-database-using-php_40.html
	for example upload form. It saves to database, but I am unable to display it.
--><div class="imgtext">
<form name ="image" action="upload.php" method="post" enctype="multipart/form-data">
     Select image to upload for your profile:
    <input type="file" name="uploadedimage" id="uploadedimage">
    <input type="submit" value="Upload Image" name="Upload Image">
</form>
</div>

<?php 


	if (isset($_POST['add_video_game'])) {
		/*Checks that name, category are all set*/
		if (empty($_POST['video_game_name'])) {
			echo 'Video game name cannot be empty<br><br>';
		}
		if (empty($_POST['video_game_cat'])) {
			echo 'Video game category cannot be empty<br><br>';
		}
		/*Assigns the name, category and rating to variables if all criteria
		were met in the form entry*/
		if (!empty($_POST['video_game_name']) && !empty($_POST['video_game_cat'])) {
			$vid_game_name = $_POST['video_game_name'];
			$vid_game_cat = $_POST['video_game_cat'];
			$vid_game_rate = $_POST['rating'];


			/*Referred to prepared statements on 
			http://php.net/manual/en/mysqli.quickstart.prepared-statements.php*/

			/* Prepared statement, stage 1: prepare from */
			if (!($stmt = $mysqli->prepare('INSERT INTO video_game_list(name, category, rating, username) VALUES (?, ?, ?, ?)'))) {
    			echo 'Prepare failed to add game to library<br><br>';
			}
			
			/* Prepared statement, stage 2: bind and execute */
			if (!($stmt->bind_param('ssis', $vid_game_name, $vid_game_cat, $vid_game_rate, $Userlogin))) {
			    echo 'Binding parameters failed to add game to library<br><br>';
			}
			if (!($stmt->execute())) {
    			echo "Cannot add \"" .$vid_game_name. "\" to list, game already exists in your library...<br><br>";
			}
		
		}
	}

	if (!$stmt = $mysqli->prepare("SELECT distinct name, category, rating FROM video_game_list WHERE username=\"$Userlogin\"")) {
		echo "Prepared failed for displaying table";
	}
	if (!$stmt->execute()) {
		echo "Execute failed for displaying table";
	}

	$out_name = NULL;
	$out_category = NULL;
	$out_rating = NULL;

	if (!$stmt->bind_result( $out_name, $out_category, $out_rating)) {
		echo 'Binding output parameters failed for displaying table';
	}

	echo '<div class=\'output\'> <table align="center">
		<caption>Video Game List</caption>
		<tr> <th> Name <th> Category <th> Rating </tr></div>';

		while($stmt->fetch()) {
			echo '<tr> <td> '.$out_name.' <td> '.$out_category.' <td> '.$out_rating.'';
		}
	echo '</table>';

	echo '<a href="content.php?action=logout">Click here to log out.</a>';
?>




</body>
</html>