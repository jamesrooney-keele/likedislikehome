<?php

require_once(__DIR__ . "/../../config.php");
if(!$adminloginenabled || $logged_in){
	header("Location: /admin/");
	exit;
}

//if they are logged in, just double check they are actually disabled rather than just coming to the page randomly
if (array_key_exists('id', $_SESSION)) {
	if ($_SESSION['id'] == session_id() && !empty($_SESSION['email']) && !empty($_SESSION['logged_in']) && $_SESSION['logged_in']) {
		if (!isset($_SESSION['useridtouse']) || $_SESSION['useridtouse'] == '') {
			$user = getUserByEmail($_SESSION['email']);
		} else {
			$user = getUserById($_SESSION['useridtouse']);
		}
		if ($user['enabled'] == '1') {
			header("Location: /");
			exit;
		}
	}
}

$loginnotrequired = true;
$pagename = "Authorisation not permitted";

include("../header.php");


echo "<div class='container'>";
echo "<h4>Unable to access portal</h4>";

echo "<p>Unable to access site.  Please <a href='/admin/login'>login</a></p>";

echo "</div>";

include("../footer.php");
