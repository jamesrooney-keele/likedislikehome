<?php
include("authorise.php");
if(!isset($logged_in) || !$logged_in){
	header("Location: /admin/");
	exit;
}

$pagename = "All users";
$users = getUsers(true);

include("../header.php");

echo "<h4>$pagename</h4>";

echo "<table class='table'><thead>";
echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th>";
echo "<th></th>";
echo "</tr></thead>";
echo "<tbody>";

foreach($users as $userid => $user){
	
		echo "<tr>";
		echo "<td>".htmlspecialchars($user['first_name'])."</td><td>".htmlspecialchars($user['last_name'])."</td>";
		echo "<td>".htmlspecialchars($user['email'])."</td>";
		echo "<td><a href='/admin/user/edit/$userid' class='btn btn-primary'>Edit user</a></td>";
		echo "</tr>";
	}

echo "</tbody></table>";

echo "<p><a href='/admin' class='btn btn-primary'>Return to admin page</a></p>";

include("../footer.php");
?>
