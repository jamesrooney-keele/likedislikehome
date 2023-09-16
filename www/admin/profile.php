<?php
include_once("authorise.php");
if (!isset($logged_in) || !$logged_in) {
	header("Location: /admin/");
	exit;
}

$pagename = "Your profile";

include("../header.php");

echo "<h3>$pagename</h3>";

if (isset($_POST['update'])) {
	$updatingpassword = false;
	$goodform = true;
	if(isset($_POST['password']) && isset($_POST['password2'] ) && $_POST['password']!='' && $_POST['password2']!=''){
		if($_POST['password']!=$_POST['password2']){
			$goodform = false;
			$errors[] = "The two passwords must match";
		}else{
			$updatingpassword = true;
		}
	}
	if($_POST['email']==''){
		$goodform = false;
		$errors[] = "Your email address cannot be blank";
	}else{
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$goodform = false;
			$errors[] = "Please enter a valid email address";
		}else{
			$dupefound = checkEmailForDupes($_POST['email'], $_SESSION['userid']);
			if($dupefound){
				$goodform = false;
				$errors[] = "That email address is already in use on the system for another user";
			}
		}
	}
	
	if(!$goodform){
		echo "<p>Unable to update profile: </p>";
		echo "<p>".implode("<br>", $errors)."</p>";
	}else{
		$statement = "UPDATE user SET first_name = :first_name, last_name = :last_name, email = :email";
		$params = array();
		$params[':first_name'] = $_POST['first_name'];
		$params[':last_name'] = $_POST['last_name'];
		$params[':email'] = $_POST['email'];
		if($updatingpassword){
			$statement .= ", password = :password";
			$params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
		}
		$statement .= " WHERE siteid IS NULL AND id = :userid";
		$params[':siteid'] = $siteid;
		$params[':userid'] = $_SESSION['userid'];
		pdoCall($statement, $params);
		echo "<p>Updated profile</p>";
	}

	echo "<p><a href='/admin/profile' class='btn btn-primary'>Update profile again</a></p>";
	echo "<p><a href='/admin' class='btn btn-primary'>Return to admin</a></p>";

} else {


	echo "<h4 class='mt-4'>Your details</h4>\n";
	echo "<form method='post' role='form' class='form-horizontal inputformsubmit'>\n";

	echo "<input name='update' value='1' type='hidden' />\n";

	echo "<div class='row mb-3'>";
	echo "<label for='first_name' class='col-md-2 control-label'>First Name</label>\n";
	echo "<div class='col-md-10'>\n";
	echo "<input type='text' name='first_name' id='first_name' placeholder='First Name' class='form-control' value='{$_SESSION['first_name']}' maxlength='200'>\n";
	echo "</div>\n";
	echo "</div>\n";

	echo "<div class='row mb-3'>";
	echo "<label for='last_name' class='col-md-2 control-label'>Last Name</label>\n";
	echo "<div class='col-md-10'>\n";
	echo "<input type='text' name='last_name' id='last_name' placeholder='Last Name' class='form-control' value='{$_SESSION['last_name']}' maxlength='200'>\n";
	echo "</div>\n";
	echo "</div>\n";

	echo "<div class='row mb-3'>";
	echo "<label for='email' class='col-md-2 control-label'>Email</label>\n";
	echo "<div class='col-md-10'>\n";
	echo "<input type='email' name='email' id='email' placeholder='Email' class='form-control' value='{$_SESSION['email']}' maxlength='200' required>\n";
	echo "</div>\n";
	echo "</div>\n";

	echo "<div class='row mb-3'>";
	echo "<label for='password' class='col-md-2 control-label'>Password</label>\n";
	echo "<div class='col-md-10'>\n";
	echo "<input type='password' name='password' id='password1' placeholder='Password' class='form-control'>\n";
	echo "<em><small>Leave blank to leave unchanged</small></em>";
	echo "</div>\n";
	echo "</div>\n";

	echo "<div class='row mb-3'>";
	echo "<label for='password' class='col-md-2 control-label'>Confirm password</label>\n";
	echo "<div class='col-md-10'>\n";
	echo "<input type='password' name='password2' id='password2' placeholder='Confirm Password' class='form-control'>\n";
	echo "<em><small>Leave blank to leave unchanged</small></em>";
	echo "</div>\n";
	echo "</div>\n";

	echo "<div class='row mb-3'>";
	echo "<div class='col-md-2'><a href='/admin/user/list' class='btn btn-primary'>Cancel</a></div>";
	echo "<div class='col-md-2'><a href='#' class='btn btn-primary formsubmit' id='formsubmitbutton'>Save</a></div>";
	echo "</div>\n";

	echo "</form>\n";
}
include("../footer.php");
