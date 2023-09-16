<?php
include("authorise.php");
if(!isset($logged_in) || !$logged_in){
	header("Location: /admin/");
	exit;
}

if(isset($_GET['userid']) && is_numeric($_GET['userid']) ){
	//check Symphony 
	$thisuserid = $_GET['userid'];
	$user = getUserById($thisuserid);
}

if(!$user){
	$pagename = "User not found";
	include("../header.php");
	echo "<p>User not found, please <a href='/'>return to the homepage</a></p>";
	include("../footer.php");
	exit;
}

$pagename = "Edit user ".htmlspecialchars($user['first_name'])." ".htmlspecialchars($user['last_name'])."";

include("../header.php");

if(isset($_POST['first_name']) && $_POST['first_name']!=''){
	
	$goodform = true;
	$error = array();
	
	if(checkEmailForDupes($_POST['email'], $thisuserid)){
		$goodform = false;
		$error[] = $_POST['email']." is already registered to another user on the system";
		
	}
	
	$changingpassword = false;
	if(!($_POST['password']=='' && $_POST['password2']=='')){
		$changingpassword = true;
	}
		
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$goodform = false;
		$error[] = $_POST['email']." is not a valid email address";
	}
	
	if($goodform){
		$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		
		$statement = "UPDATE users SET email = :email, first_name = :first_name, last_name = :last_name";
		$statement .= ", enabled = :enabled, admin = :admin, author = :author, editor = :editor, editor_chief = :editor_chief";
		if($changingpassword){
			$statement .= ", password = :password";
		}
		$statement .= " WHERE siteid IS NULL AND id = :userid";
		$params = array();
		$params[':email'] = $_POST['email'];
		$params[':first_name'] = $_POST['first_name'];
		$params[':last_name'] = $_POST['last_name'];
		$params[':status'] = (isset($_POST['disabled']) && $_POST['disabled']=='on') ? 0 : 9;
		$params[':admin'] = (isset($_POST['admin']) && $_POST['admin']=='on') ? 1 : 0;
		if($changingpassword){
			$params[':password'] = $hashed_password;
		}
		$params[':siteid'] = $siteid;
		$params[':userid'] = $user['id'];
		pdoCall($statement, $params);
		
		if(is_null($sth->errorInfo()[2])){
			echo "<div class='alert alert-success'>Succesfully updated ".htmlspecialchars($_POST['first_name'])." ".htmlspecialchars($_POST['last_name'])."</div>";
		}else{
			echo "<div class='alert alert-danger'>Unable to update user: ".$sth->errorInfo()[2]."</div>";
		}
		
	}else{
		echo "<p>Unable to update the user: <br>";
		echo implode("<br>", $error);
		echo "</p>";
		echo "<p><a class='btn btn-primary' href='javascript:history:back();'>Go back</a></p>";
	}
	
	echo "<p><a class='btn btn-primary' href='/admin/user/edit/$thisuserid'>Further edit the user</a></p>";
	
	echo "<p><a href='/admin/user/list' class='btn btn-primary'>Return to the list of users</a></p>";
	
	echo "<p><a class='btn btn-primary' href='/admin/configuration'>Return to the configuration page</a></p>";
	
}else{
	echo "<h3>$pagename</h3>";
	
	if($user['security']>9){
		echo "<div class='row'><div class='col-12 text-center font-italic'>This user is an administrative account required for support so should not be disabled</div></div>";
	}
	
	echo "<form method='post' enctype='multipart/form-data' class='inputformsubmit' >";
	
	echo "<input type='hidden' name='userid' id='userid' value='$thisuserid' />";
	
	echo "<div class='row mb-3'>";
		echo "<label for='user_email' class='col-md-2 col-form-label'>Email address</label>";
		echo "<div class='col-md-9'>";
			echo "<input type='email' id='user_email' name='email' placeholder='Email address' class='form-control' maxlength='150' value='".htmlspecialchars($user['email'])."' required>";
			echo "<small id='emailHelp' style='display:none' class='form-text text-muted'></small>";
		echo "</div>";
		echo "<div class='col-md-1'>";
			echo "<span id='emailsuccess' class='text-success'><i class='fa fa-2x fa-check' aria-hidden='true'></i></span>";
			echo "<span id='emailfail' class='text-danger' style='display:none;' ><i class='fa fa-2x fa-times' aria-hidden='true'></i></span>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='row mb-3'>";
		echo "<label for='password1' class='col-md-2 col-form-label'>Password</label>";
		echo "<div class='col-md-10'>";
			echo "<input type='password' id='password1' name='password' placeholder='Password' class='form-control'>";
			echo "<small id='passwordHelp' class='form-text text-muted'>Leave blank to not change</small>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='row mb-3'>";
		echo "<label for='password2' class='col-md-2 col-form-label'>Confirm Password</label>";
		echo "<div class='col-md-10'>";
			echo "<input type='password' id='password2' name='password2' placeholder='Confirm Password' class='form-control'>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='row mb-3'>";
		echo "<label for='first_name' class='col-md-2 col-form-label'>First Name</label>";
		echo "<div class='col-md-10'>";
			echo "<input type='text' id='first_name' name='first_name' placeholder='First Name' maxlength='100' class='form-control' value='".htmlspecialchars($user['first_name'])."' required>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='row mb-3'>";
		echo "<label for='last_name' class='col-md-2 col-form-label'>Last Name</label>";
		echo "<div class='col-md-10'>";
			echo "<input type='text' id='last_name' name='last_name' placeholder='Last Name' maxlength='100' class='form-control' value='".htmlspecialchars($user['last_name'])."' required>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='row mb-3'>";
		echo "<label for='disabled' class='col-md-2 col-form-label'>Disabled</label>";
		echo "<div class='col-md-10'>";
			echo "<div class='form-check'>";
				echo "<input type='checkbox' id='disabled' name='disabled' class='form-check-input largecheckbox'";
				if($user['status']==0){
					echo " checked='checked'";
				}
				echo ">";
			echo "</div>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='row mb-3'>";
		echo "<div class='col-md-2'><a href='/admin/user/list' class='btn btn-primary'>Cancel</a></div>";
		echo "<div class='col-md-10'><input type='button' class='formsubmit btn btn-primary' id='formsubmitbutton' value='Update' /></div>";
	echo "</div>";
	
	echo "</form>";
	
}

include("../footer.php");
?>
