<?php
$loginnotrequired = true;
session_start();
session_destroy();

include_once("authorise.php");

if(isset($logged_in) && $logged_in){
	header("Location: .");
	exit;
}

$key = (isset($_GET['key'])) ? $_GET['key'] : "";
$email = (isset($_GET['email'])) ? $_GET['email'] : "";

if($key=='' || !isset($key)){
	header("Location: .");
	exit;
}

$pagename = "Reset Password";
$validreset = false;

if($email=='' || $key == ''){	
	$error = "<p>The link was not properly formed, please copy the entire link from your email</p>\n";
}else{
	$user = getUserByEmail($email);
	if($user){
		if($user['confirmationkey']==$key){
			$expiry = strtotime($user['confirmationkey_expiry']);
			if(time()<$expiry){
				$validreset = true;
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

include("../header.php");

if($validreset){
	

	
	?>

	<div class="container">
		<h2>Password reset</h2>
		<form role="form" action='passwordreset' method='post' class='inputformsubmit'>
			<div class="mb-3 row">
				<label class='col-12 col-form-label' for="password">Password:</label>
				<div class='col-12'>
					<input type="password" class="form-control" id="password1" name="password" placeholder="Enter password">
				</div>
			</div>
			<div class="mb-3 row">
				<label class='col-12 col-form-label' for="password2">Confirm password:</label>
				<div class='col-12'>
					<input type="password" class="form-control" id="password2" name="password2" placeholder="Confirm password">
				</div>
			</div>
			<input type='hidden' name='email' value='<?php echo $email; ?>' />
			<input type='hidden' name='key' value='<?php echo $key; ?>' />
			<button type="submit" class="btn btn-primary" id='formsubmitbutton'>Reset password</button>
		</form>
	</div>

	<?php
}else{
	echo "<h2>Password reset</h2>\n";
	
	echo "<p>$error</p>\n";
	
	echo "<p><a href='/forgot'>Send another password reset</a></p>";
	
}
include("../footer.php");
?>
