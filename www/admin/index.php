<?php
require_once("authorise.php");
$pagename = "Admin";
include("../header.php");


echo "<div class='row'>";

echo "<div class='col-12'>";

echo "<h4>Current sites</h4>";

$statement = "SELECT * FROM sites";
$sth = pdoCall($statement);
$sites = array();
while($result = $sth->fetch(PDO::FETCH_ASSOC)){
    $sites[] = $result;
}

echo "<p><a href='/admin/site/add' class='btn btn-primary'>Add a new site</a></p>";

echo "<table class='table'>";
echo "<thead>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>URL</th>";
echo "<th>Dates enabled</th>";
echo "<th>Admin requires password</th>";
echo "<th>Status</th>";
echo "<th></th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach($sites as $site){
    echo "<tr>";
    echo "<td>{$site['id']}</td>";
    echo "<td>{$site['url']}</td>";
    echo "<td>";
    echo "Open from: ".explode(" ", $site['start_date'])[0];
    echo "<br>Closes after: ".explode(" ", $site['end_date'])[0];
    echo "</td>";
    echo "<td>";
    if($site['status']=='0'){
        echo "Enabled";
    }else{
        echo "Disabled";
    }
    echo "</td>";
    echo "<td><a href='/admin/site/edit/{$site['id']}' class='btn btn-primary'>Edit site</a></td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";

echo "<h4>Users</h4>";
echo "<p>This is the list of users for THIS ADMIN SITE only</p>";
echo "<p><a href='/admin/user/list' class='btn btn-primary'>List of users</a></p>";


echo "</div>";
echo "</div>";


include("../footer.php");
