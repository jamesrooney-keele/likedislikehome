<?php

$loginnotrequired = true;
include_once("authorise.php");

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']){
	header("Location: .");
	exit;
}

$pagename = "Login";
include("../header.php");
?>
<div class='container'>

<div class='text-center'>
	<h2 id='pageheader'><?php echo $pagename; ?></h2>
	<div class='row'>
		<div class='col-12'><br>
		Please login to view the site
		<br><br>
		</div>
	</div>
</div>
<?php
if(isset($_SESSION['loginerror']) && $_SESSION['loginerror']!=''){
	echo "<div class='row'><div class='col-12 alert alert-danger'>";
	echo $_SESSION['loginerror'];
	echo "</div></div>";
}
?>

<div class='row'><div class='col-6 offset-3'>

<form role="form" action='authenticate.php' method='post' class='inputformsubmit'>
	<div class="mb-3 row">
		<label class='col-md-3 col-form-label' for="email">Email address:</label>
		<div class='col-md-9'>
			<input type="email" class="form-control inputformsubmit" id="email" name="email" placeholder="Enter email" required>
		</div>
	</div>
	
	<div class="mb-3 row">
		<label class='col-md-3 col-form-label' for="password">Password:</label>
		<div class='col-md-9'>
			<input type="password" class="form-control inputformsubmit" id="password" name="password" placeholder="Enter password" required>
		</div>
	</div>
	
	<div class="mb-3 row">
		<label class='col-md-3 col-form-label' for="remember_me">Remember me:</label>
		<div class='col-md-9'>
			<div class='form-check'>
				<input type="checkbox" id="remember_me" name="remember_me" class='largecheckbox form-check-input'>
			</div>
		</div>
	</div>
		
	<?php
	echo "<input type='hidden' name='referrer' ";
	if(isset($_REQUEST['referrer'])){
		echo " value='".$_REQUEST['referrer']."'";
	}
	echo "/>";
	?>
		
	<div class='col-12 col-lg-4'>
			
			<a href='#' id='formsubmitbutton' class='btn btn-primary formsubmit'>Log In</a>
			
			<p><br><a href='forgot' >Forgotten password</a></p>
			
		</div>
</form>

</div></div> <!-- end center col -->

</div>
<?php
include("../footer.php");
?>
