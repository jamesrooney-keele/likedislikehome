<?php
require_once(__DIR__ . "/../../config.php");

$password = $_POST['password'];
$email = $_POST['email'];
$remembermeexpiry = 30;  //days to remember cookie for

session_destroy(); //make sure any old session data is removed
session_start();
$location = 'badauth';

if ($email != '' && $password != '') {
	$user = getUserByEmail($email);
	if ($user) {
		$_SESSION['loginerror'] = "";
		if (password_verify($password, $user['password'])) {
			session_regenerate_id();
			if ($_POST['referrer'] != '') {
				$location = $_POST['referrer'];
			} else {
				$location = ".";
			}

			$_SESSION['userid'] = $user['id'];
			$_SESSION['first_name'] = $user['first_name'];
			$_SESSION['last_name'] = $user['last_name'];
			$_SESSION['email'] = $user['email'];
			$_SESSION['logged_in'] = true;
			$_SESSION['id'] = session_id();

			//if remember me set
			if (array_key_exists("remember_me", $_POST)) {
				if ($_POST['remember_me'] == 'on') {

					setcookie("userid", $user['id'], time() + 60 * 60 * 24 * $remembermeexpiry, '/', $_SERVER['SERVER_NAME'], true, true);
					setcookie("Email", $email, time() + 60 * 60 * 24 * $remembermeexpiry, '/', $_SERVER['SERVER_NAME'], true, true);
					setcookie("CheckID", password_hash($user['id'] . " ldsnskbflsdcdslfn " . $email, PASSWORD_DEFAULT), time() + 60 * 60 * 24 * $remembermeexpiry, '/', $_SERVER['SERVER_NAME'], true, true);
				}
			}
		} else {
			$_SESSION['loginerror'] = "Username or password not found. If you have forgotten your password please click on the <a href='/admin/forgot'>forgotten password</a> link and follow the instructions.";
		}
	}
} else {
	$_SESSION['loginerror'] = "Your username and password cannot be blank.";
}
header("Location: $location");
exit;
