<?php
include("authorise.php");
if(!isset($logged_in) || !$logged_in){
	header("Location: /admin/");
	exit;
}

$pagename = "Add a new user";
include("../header.php");

if(isset($_POST['update'])){
	
	$goodform = true;
	$error = array();
	
	if(checkEmailForDupes($_POST['email'], -1)){
		$goodform = false;
		$error[] = $_POST['email']." is already registered to another user on the system";
	}
	
	if(is_null($_POST['password']) || $_POST['password']==''|| $_POST['password'] != $_POST['password2']){
		$error = "The password cannot be blank, and the two passwords must match";
		$valid = false;
	}
	
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$goodform = false;
		$error[] = $_POST['email']." is not a valid email address";
	}
	
	if($goodform){
		$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		
		//Assume users are email validated if they are being added by admin
		$statement = "INSERT INTO users(email,first_name,last_name,password,admin) VALUES
									(:email,:first_name,:last_name,:password,:admin)";
	
		$params = array();
		$params[':email'] = $_POST['email'];
		$params[':first_name'] = $_POST['first_name'];
		$params[':last_name'] = $_POST['last_name'];
		$params[':password'] = $hashed_password;
		$thisuserid = pdoCall($statement, $params);
		
		if(is_numeric($thisuserid) && $thisuserid>0){

			echo "<p>Succesfully added user</p>";
			
			echo "<p><a class='btn btn-primary' href='/admin/user/edit/$thisuserid'>Further edit this user</a></p>";
			
		}else{
			echo "<p>Unable to add user: ".$sth->errorInfo()[2]."</p>";
			echo "<p><a class='btn btn-primary' href='/admin/user/add'>Go back</a></p>";
		}
		
	}else{
		echo "<p>Unable to add the user: <br>";
		echo implode("<br>", $error);
		echo "</p>";
		echo "<p><a class='btn btn-primary' href='/admin/user/add'>Go back</a></p>";
	}
	
	echo "<p><a href='/admin/user/list' class='btn btn-primary'>Return to the list of users</a></p>";
	
	echo "<p><a class='btn btn-primary' href='/admin/'>Return to the admin page</a></p>";
	
}else{
	echo "<h3>$pagename</h3>";
	
	echo "<form action='/admin/user/add' method='post' autocomplete='off' class='inputformsubmit' >";
	
	echo "<input type='hidden' name='userid' id='userid' value='-1' />";
	echo "<input type='hidden' name='update' value='1' />";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='user_email' class='col-2 col-form-label'>Email address</label>";
		echo "<div class='col-9'>";
			echo "<input type='email' id='user_email' name='email' placeholder='Email address' class='form-control' maxlength='150' autocomplete='off' required>";
			echo "<small id='emailHelp' style='display:none' class='form-text text-muted'></small>";
		echo "</div>";
		echo "<div class='col-1'>";
			echo "<span id='emailsuccess' class='text-success' style='display:none;'><i class='fa fa-2x fa-check' aria-hidden='true'></i></span>";
			echo "<span id='emailfail' class='text-danger' ><i class='fa fa-2x fa-times' aria-hidden='true'></i></span>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='password' class='col-2 col-form-label'>Password</label>";
		echo "<div class='col-10'>";
			echo "<input type='password' id='password1' name='password' placeholder='Password' class='form-control' autocomplete='off' required>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='password2' class='col-2 col-form-label'>Confirm Password</label>";
		echo "<div class='col-10'>";
			echo "<input type='password' id='password2' name='password2' placeholder='Confirm Password' class='form-control' required>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='first_name' class='col-2 col-form-label'>First Name</label>";
		echo "<div class='col-10'>";
			echo "<input type='text' id='first_name' name='first_name' placeholder='First Name' maxlength='100' class='form-control' required>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='last_name' class='col-2 col-form-label'>Last Name</label>";
		echo "<div class='col-10'>";
			echo "<input type='text' id='last_name' name='last_name' placeholder='Last Name' maxlength='100' class='form-control' required>";
		echo "</div>";
	echo "</div>";

	echo "<h4>Permissions</h4>";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='admin' class='col-2 col-form-label'>System Admin</label>";
		echo "<div class='col-10'>";
			echo "<div class='form-check'>";
				echo "<input type='checkbox' id='admin' name='admin' class='form-check-input largecheckbox'>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='author' class='col-2 col-form-label'>Author</label>";
		echo "<div class='col-10'>";
			echo "<div class='form-check'>";
				echo "<input type='checkbox' id='author' name='author' class='form-check-input largecheckbox'>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='editor' class='col-2 col-form-label'>Editor</label>";
		echo "<div class='col-10'>";
			echo "<div class='form-check'>";
				echo "<input type='checkbox' id='editor' name='editor' class='form-check-input largecheckbox'>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='mb-3 row'>";
		echo "<label for='editor_chief' class='col-2 col-form-label'>Editor Chief</label>";
		echo "<div class='col-10'>";
			echo "<div class='form-check'>";
				echo "<input type='checkbox' id='editor_chief' name='editor_chief' class='form-check-input largecheckbox'>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='mb-3 row'>";
		echo "<div class='col-2'><a href='/admin/user/list' class='btn btn-primary'>Cancel</a></div>";
		echo "<div class='col-10'><input type='button' class='formsubmit btn btn-primary' id='formsubmitbutton' disabled='disabled' value='Add User' /></div>";
	echo "</div>";
	
	echo "</form>";
	
}

include("../footer.php");
?>
