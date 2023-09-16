<?php
include_once(__DIR__ . "/../../config.php");

$logged_in = false;
$adminloginenabled = false;
if (array_key_exists('id', $_SESSION)) {
	if ($_SESSION['id'] == session_id() && !empty($_SESSION['email']) && !empty($_SESSION['logged_in']) && $_SESSION['logged_in']) {

		$user = getUserByEmail($_SESSION['email']);
		$logged_in = true;
	}
} else {
	//check cookie
	if (isset($_COOKIE['CheckID']) && isset($_COOKIE['userid']) && isset($_COOKIE['Email'])) {

		if (password_verify($_COOKIE['userid'] . " ldsnskbflsdcdslfn " . $_COOKIE['Email'], $_COOKIE['CheckID'])) {

			$user = getUserById($_COOKIE['userid']);

			if ($user) {
				//set session variables based on userid in cookie if all looks to be correct
				$_SESSION['userid'] = $user['id'];
				$_SESSION['first_name'] = $user['first_name'];
				$_SESSION['last_name'] = $user['last_name'];
				$_SESSION['email'] = $user['email'];
				$_SESSION['logged_in'] = true;
				$_SESSION['id'] = session_id();
				$logged_in = true;
			}
		}
	}
}

if ($logged_in) {
	$security = $user['security'];

	if ($user['status'] == 9) {
		header("Location: /admin/noauth");
		exit;
	}
} else {
	//not logged in, do we need to be?

	if (!isset($loginnotrequired) && !isset($noredirect)) {
		header("Location: /admin/login");
		exit;
	}
}
