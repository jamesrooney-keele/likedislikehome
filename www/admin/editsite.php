<?php
include("authorise.php");
if (!isset($logged_in) || !$logged_in) {
    header("Location: /admin/");
    exit;
}

if(isset($_GET['siteid']) && is_numeric($_GET['siteid']) ){
	//check Symphony 
	$thissiteid = $_GET['siteid'];
	$site = getSiteById($thissiteid);
}

if(!$site){
	$pagename = "Site not found";
	include("../header.php");
	echo "<p>Site not found, please <a href='/'>return to the homepage</a></p>";
	include("../footer.php");
	exit;
}
$tinymce = true;

$pagename = "Update site ".$site['url'];
include("../header.php");

if (isset($_POST['update'])) {

    $goodform = true;
    $error = array();

    if (checkSiteForDupes($_POST['url'], $thissiteid)) {
        $goodform = false;
        $error[] = $_POST['url'] . " is already registered to another site";
    }

    if ($_POST['title'] == '') {
        $goodform = false;
        $error[] = "The site title cannot be blank";
    }

    if ($_POST['topleft'] == '') {
        $goodform = false;
        $error[] = "The top left text cannot be blank";
    }

    if ($_POST['topright'] == '') {
        $goodform = false;
        $error[] = "The top right text cannot be blank";
    }

    if ($_POST['bottomleft'] == '') {
        $goodform = false;
        $error[] = "The bottom left text cannot be blank";
    }

    if ($_POST['bottomright'] == '') {
        $goodform = false;
        $error[] = "The bottom right text cannot be blank";
    }

    if ($goodform) {

        //Assume users are email validated if they are being added by admin
        $statement = "UPDATE sites SET
                        url = :url,
                        title = :title,
                        homepagetext = :homepagetext,
                        adminpasswordrequired = :adminpasswordrequired,
                        topleft = :topleft,
                        topright = :topright,
                        bottomright = :bottomright,
                        bottomleft = :bottomleft,
                        start_date = :start_date,
                        end_date = :end_date
                    WHERE id = :siteid";

        $params = array();
        $params[':url'] = $_POST['url'];
        $params[':title'] = $_POST['title'];
        $params[':homepagetext'] = $_POST['homepagetext'];
        $params[':adminpasswordrequired'] = (isset($_POST['adminpasswordrequired']) && strtolower($_POST['adminpasswordrequired']) == 'on') ? 1 : 0;
        $params[':topleft'] = $_POST['topleft'];
        $params[':topright'] = $_POST['topright'];
        $params[':bottomright'] = $_POST['bottomright'];
        $params[':bottomleft'] = $_POST['bottomleft'];
        $params[':start_date'] = $_POST['start_date']." 00:00:00";
        $params[':end_date'] = $_POST['end_date']." 23:59:59";
        $params[':siteid'] = $thissiteid;
        $sth = pdoCall($statement, $params);
		
		if(is_null($sth->errorInfo()[2])){
			echo "<div class='alert alert-success'>Succesfully updated ".htmlspecialchars($_POST['title'])."</div>";
		}else{
			echo "<div class='alert alert-danger'>Unable to update site: ".$sth->errorInfo()[2]."</div>";
		}
    } else {
        echo "<p>Unable to update the site: <br>";
        echo implode("<br>", $error);
        echo "</p>";
        echo "<p><a class='btn btn-primary' href='/admin/site/edit/$thissiteid'>Go back</a></p>";
    }

    echo "<p><a href='/admin/' class='btn btn-primary'>Return to the list of sites</a></p>";
} else {
    echo "<h3>$pagename</h3>";

    echo "<form method='post' autocomplete='off' class='inputformsubmit' >";

    echo "<input type='hidden' name='siteid' id='siteid' value='$thissiteid' />";
    echo "<input type='hidden' name='update' value='1' />";

    echo "<div class='mb-3'>";
    echo "<label for='url' class='form-label'>Site url</label>";
    echo "<div class='row'>";
    echo "<div class='col-11'>";
    echo "<input type='text' id='url' name='url' placeholder='Site URL' class='form-control' maxlength='150' autocomplete='off' value='{$site['url']}' required>";
    echo "<small id='urlHelp' style='display:none' class='form-text text-muted'></small>";
    echo "</div>";
    echo "<div class='col-1'>";
    echo "<span id='urlsuccess' class='text-success'><i class='fa fa-2x fa-check' aria-hidden='true'></i></span>";
    echo "<span id='urlfail' class='text-danger' style='display:none;' ><i class='fa fa-2x fa-times' aria-hidden='true'></i></span>";
    echo "</div>";
    echo "<div class='col-12'><small>Any site here which is in the format *.likedislike.co.uk will autmatically work straight away assuming the URL is not already taken.  Any others you need to contact James to configure</small></div>";
    echo "</div>";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='start_date' class='form-label'>Open from</label>";
    echo "<input type='date' id='start_date' name='start_date' placeholder='Start date' class='form-control' autocomplete='off' value='".explode(" ", $site['start_date'])[0]."' required>";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='end_date' class='form-label'>Closes after</label>";
    echo "<input type='date' id='end_date' name='end_date' placeholder='End date' class='form-control' autocomplete='off' value='".explode(" ", $site['end_date'])[0]."' required>";
    echo "</div>";

    echo "<h4>Site settings</h4>";

    echo "<div class='mb-3'>";
    echo "<label for='title' class='form-label'>Site title</label>";
    echo "<input type='text' class='form-control' id='title' name='title' value='{$site['title']}' required />";
    echo "</div>";
    
    echo "<div class='form-check'>";
    echo "<input class='form-check-input' type='checkbox' name='adminpasswordrequired' id='adminpasswordrequired'";
    if($site['adminpasswordrequired']==1){
        echo " checked='checked'";
    }
    echo ">\n";
    echo "<label class='form-check-label' for='adminpasswordrequired'  >\n";
    echo "Require a password to get into the admin site\n";
    echo "</label>\n";
    echo "</div>\n";

    echo "<div class='mb-3 mt-4'>";
    echo "<label for='homepagetext' class='form-label'>Homepage/instructions text</label>";
    echo "<textarea class='tinymce' name='homepagetext' id='homepagetext'>{$site['homepagetext']}</textarea>";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='topleft' class='form-label'>Top left text</label>";
    echo "<input type='text' class='form-control' value='Love' id='topleft' name='topleft' value='{$site['topleft']}' required />";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='topright' class='form-label'>Top right text</label>";
    echo "<input type='text' class='form-control' value='Love and hate' id='topright' name='topright' value='{$site['topright']}' required />";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='bottomright' class='form-label'>Bottom right text</label>";
    echo "<input type='text' class='form-control' value='Hate' id='bottomright' name='bottomright' value='{$site['bottomright']}' required />";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='bottomleft' class='form-label'>Bottom left text</label>";
    echo "<input type='text' class='form-control' value='No feelings either way' id='bottomleft' value='{$site['bottomleft']}' name='bottomleft' required />";
    echo "</div>";

    echo "<div class='form-check mb-3'>";
    echo "<input class='form-check-input' type='checkbox' name='disabled' id='disabled'>\n";
    echo "<label class='form-check-label' for='disabled' ";
    if($site['status']=='9'){
        echo " checked='checked'";
    }
    echo " >\n";
    echo "Site Disabled\n";
    echo "</label>\n";
    echo "</div>\n";

    echo "<div class='mb-3 row'>";
    echo "<div class='col-2'><a href='/admin/' class='btn btn-primary'>Cancel</a></div>";
    echo "<div class='col-10'><input type='button' class='formsubmit btn btn-primary' id='savesite' value='Update' /></div>";
    echo "</div>";

    echo "</form>";
}

include("../footer.php");
