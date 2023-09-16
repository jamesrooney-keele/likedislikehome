<?php
$loginnotrequired = true;
include_once("authorise.php");

if(isset($logged_in) && $logged_in){
	header("Location: .");
	exit;
}

$pagename = "Forgotten Password";

include("../header.php");

if(isset($_POST['email'])){
	
	$email = $_POST['email'];
	
	$user = getUserByEmail($email);
	
	if($user){
		$confirmationkey = rand_string(20);
		$statement = "UPDATE user SET confirmationkey = :confirmationkey, confirmationkey_expiry = NOW() + INTERVAL 24 HOUR WHERE siteid = :siteid AND email = :email";
		
		$params = array();
		$params[':confirmationkey'] = $confirmationkey;
		$params[':siteid'] = $siteid;
		$params[':email'] = $email;
		$sth = pdoCall($statement, $params);
		
		$to = $email;
		$subject = "Reset password for the Like Dislike website";
		$body = "Please click the link below or copy it into your browser to reset your password.  This link will only be valid for 24 hours<br><br>";
		$link = "http";
		if($_SERVER['HTTPS']=='on'){
			$link .= "s";
		}
		$link .= "://".$_SERVER['SERVER_NAME']."/admin/passwordconfirm.php?email=$email&key=$confirmationkey";
		$body .= "<a href='$link'>$link</a>";
		
		sendEmail($to, $subject, $body, array(), "forgotpassword", null);
	}
	
	
	?>

	<div class="container">
		<h2 id='pageheader'>Forgotten password</h2>
		<p>If your email address is already registered and you have an active account, then a password reset email will be sent to you. Please check your emails. 
				If you don't receive an email and have not previously registered an account, you will need to create a log in.</p>
	</div>

	<?php
}else{
	?>

	<div class="container">
		<h2 id='pageheader'>Forgotten password</h2>
		<form role="form" action='forgot' method='post' class='inputformsubmit'>
			<div class="row mb-3">
				<label for="email" class='col-12 col-form-label'>
				Please enter your email address below to have a password resent email sent to you
				</label>
				<div class='col-12'>
					<input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
					</div>
			</div>
			<button type="submit" class="btn btn-primary" id='formsubmitbutton'>Reset password</button>
		</form>
	</div>

	<?php
	
}
include("../footer.php");
?>
