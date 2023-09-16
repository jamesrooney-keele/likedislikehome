<?php
$loginnotrequired = true;
include_once("authorise.php");

if(isset($logged_in) && $logged_in){
	header("Location: .");
	exit;
}


$key = (isset($_POST['key'])) ? $_POST['key'] : "";
$email = (isset($_POST['email'])) ? $_POST['email'] : "";
$password = (isset($_POST['password'])) ? $_POST['password'] : "";
$password2 = (isset($_POST['password2'])) ? $_POST['password2'] : "";
$validreset = false;

if($email=='' || $key == ''){	
	$error = "<p>The link was not properly formed, please copy the entire link from your email and try again</p>\n";
}elseif($password=='' || $password2==''){
	$error = "<p>The password must not be blank</p>";
}else{
	$user = getUserByEmail($email);
	if($user){
		if($user['confirmationkey']==$key){
			$expiry = strtotime($user['confirmationkey_expiry']);
			if(time()<$expiry){
				if($password==$password2){
					$validreset = true;
				}else{
					$error = "The passwords do not match";
				}
			}else{
				$error = "That key has expired, please request new password reset key";
			}
		}else{
			$error = "Sorry, the reset key was not recognised, please try again, the key may have expired";
		}
	}else{
		$error = "Sorry, the reset key was not recognised, please try again, the key may have expired";
	}
}

$pagename = "Reset Password";

include("../header.php");

echo "<div class='container'>";

echo "<h2>$pagename</h2>\n";

if($validreset){
	
	//if not validated email, count this as good enough
	$password_hash = password_hash($password, PASSWORD_DEFAULT);
	
	$statement = "UPDATE user SET password = :password, confirmationkey = '', confirmationkey_expiry = null WHERE siteid = :siteid AND id = :userid";
	$params = array();
	$params[':siteid'] = $siteid;
	$params[':password'] = $password_hash;
	$params[':userid'] = $user['id'];
	$sth = pdoCall($statement, $params);
	session_regenerate_id(true);
	
	echo "<p>Password succesfully updated.  Please <a href='login'>Login</a> with your new password</p>\n";
}else{	
	echo "<p>$error</p>\n";
	
	echo "<p><a href='/forgot'>Send another password reset</a></p>";
	
}

echo "</div>";
include("../footer.php");
?>
