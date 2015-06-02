<?php
session_save_path(“/tmp”); session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require ('logininfo.php');

$mysqli = new mysqli($host, $user, $pw, $dbname);
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL with error number: " .$mysqli->connect_errno. "<br>";
} 

/*Checks whether to sign in or register an account depending on request action*/
if (isset($_REQUEST['action'])) {
	if (($_REQUEST['action']) == 'Userlogin') {
		$Userlogin = $_REQUEST['username'];
		$Userpassword = SHA1($_REQUEST['password']);/*Added SHA1 hashing to password check*/
		sign_in($Userlogin, $Userpassword);
		
	}
	else if (($_REQUEST['action']) == 'Register_account') {
		
		$Userlogin = $_REQUEST['username'];
		
		if(strlen($Userlogin) < 6 ) { /*Checks to make sure username is at least 6 characters*/
			echo 'Username needs to be atleast 6 characters long';
			exit();
		}
		$Userpassword = $_REQUEST['password'];
		if(strlen($Userpassword) < 8) {   /*Checks to make sure password is at least 8 characters*/
			echo 'Password needs to be atleast 8 characters long';
			exit();
		}

		$Userpassword = SHA1($_REQUEST['password']);/*Added SHA1 hasing to password compare*/
		register_account($Userlogin, $Userpassword);

	}
}
?>


<?php
/*MySQLi queries for signing into an account*/
function sign_in ($Userlogin, $Userpassword) {
	global $mysqli;
	
	if(!($check = $mysqli->prepare("SELECT * from USERPASS WHERE username= ? AND password = ?"))) {
		echo "cannot prepare check";
	}
	if(!($check->bind_param('ss', $Userlogin, $Userpassword))) {  
		echo "cannot bind param check";
	}
	if(!($check->execute())) {
		echo "cannot execute check";
	}
	if(!($result = $check->get_result())) {
		echo "cannot get result check";
	}
	$userResult = $result->fetch_assoc();
	if ($userResult['username'] === $Userlogin && $userResult['password'] === $Userpassword) {  
		 
		$_SESSION['Userlogin'] = $Userlogin;

		echo '<p><a href="content.php">Click here to access your personal game library</a></p>';

	} else {
		echo "<br>The username and/or password combination is not found";
	}
	$check->close();
}

/*MYSQLi queries for registering an account*/
function register_account($Userlogin, $Userpassword) {
	global $mysqli;

	if(!($check = $mysqli->prepare("SELECT * from USERPASS WHERE username= ?"))) {
		echo "cannot prepare check user";
	}
	if(!($check->bind_param('s', $Userlogin))) {
		echo "cannot bind param check bind";
	}
	if(!($check->execute())) {
		echo "cannot execute check exe";
	}
	if(!($result = $check->get_result())) {
		echo "cannot get result check to see if it is in before register";
	}
	$userResult = $result->fetch_assoc();
	if ($userResult['username'] === $Userlogin) {
		
		echo '<br>Username already exists, register a different username';
		
		$check->close();

		exit;

	} 
	if ($userResult['username'] != $Userlogin) {
		$check->close();

		if(!($register = $mysqli->prepare("INSERT INTO USERPASS (username, password) VALUES (?,?)"))) {
			echo "cannot prepare register";
			
		}
		else if(!($register->bind_param('ss', $Userlogin, $Userpassword))) {
			echo "cannot bind param register";
			
		}
		else if(!($register->execute())) {
			echo "cannot execute register";
			
		}
	
		else {
		echo '<p>Registration completed<br></p>
			  <p><a href="newindex.html">Click here to login</a></p>';	
		}	
		$register->close();
	}
	
	$mysqli->close();
	}
?>

