<?php
include("authorise.php");
if (!isset($logged_in) || !$logged_in) {
    header("Location: /admin/");
    exit;
}
$tinymce = true;

$pagename = "Add a new site";
include("../header.php");

if (isset($_POST['update'])) {

    $goodform = true;
    $error = array();

    if (checkSiteForDupes($_POST['url'], -1)) {
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
        $statement = "INSERT INTO sites(url,title,homepagetext,adminpasswordrequired,topleft,topright,bottomright,bottomleft,start_date,end_date) VALUES
									(:url,:title,:homepagetext,:adminpasswordrequired,:topleft,:topright,:bottomright,:bottomleft,:start_date,:end_date)";

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
        $thissiteid = pdoCall($statement, $params);

        if (is_numeric($thissiteid) && $thissiteid > 0) {

            echo "<p>Succesfully added site</p>";

            $statement = "INSERT INTO user(siteid,email,password) VALUES(:siteid,:email,:password)";
            $params = array();
            $params[':siteid'] = $thissiteid;
            //james
            $params[':email'] = "james_rooney@hotmail.com";
            $params[':password'] = password_hash('james21', PASSWORD_DEFAULT);
            pdoCall($statement, $params);
            //gordon
            $params[':email'] = "gordon@eidesis.co.uk";
            $params[':password'] = password_hash('gordon21', PASSWORD_DEFAULT);
            pdoCall($statement, $params);
            //steph
            $params[':email'] = "stephaniedale@gmail.com";
            $params[':password'] = password_hash('steph21', PASSWORD_DEFAULT);
            pdoCall($statement, $params);

            echo "<p><a class='btn btn-primary' href='/admin/site/edit/$thissiteid'>Further edit this site</a></p>";
        } else {
            echo "<p>Unable to add site: " . $sth->errorInfo()[2] . "</p>";
            echo "<p><a class='btn btn-primary' href='/admin/site/add'>Go back</a></p>";
        }
    } else {
        echo "<p>Unable to add the site: <br>";
        echo implode("<br>", $error);
        echo "</p>";
        echo "<p><a class='btn btn-primary' href='/admin/site/add'>Go back</a></p>";
    }

    echo "<p><a href='/admin/' class='btn btn-primary'>Return to the list of sites</a></p>";
} else {
    echo "<h3>$pagename</h3>";

    echo "<form method='post' autocomplete='off' class='inputformsubmit' >";

    echo "<input type='hidden' name='siteid' id='siteid' value='-1' />";
    echo "<input type='hidden' name='update' value='1' />";

    echo "<div class='mb-3'>";
    echo "<label for='url' class='form-label'>Site url</label>";
    echo "<div class='row'>";
    echo "<div class='col-11'>";
    echo "<input type='text' id='url' name='url' placeholder='Site URL' class='form-control' maxlength='150' autocomplete='off' required>";
    echo "<small id='urlHelp' style='display:none' class='form-text text-muted'></small>";
    echo "</div>";
    echo "<div class='col-1'>";
    echo "<span id='urlsuccess' class='text-success' style='display:none;'><i class='fa fa-2x fa-check' aria-hidden='true'></i></span>";
    echo "<span id='urlfail' class='text-danger' ><i class='fa fa-2x fa-times' aria-hidden='true'></i></span>";
    echo "</div>";
    echo "<div class='col-12'><small>Any site here which is in the format *.likedislike.co.uk will autmatically work straight away assuming the URL is not already taken.  Any others you need to contact James to configure</small></div>";
    echo "</div>";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='start_date' class='form-label'>Open from</label>";
    echo "<input type='date' id='start_date' name='start_date' placeholder='Start date' class='form-control' autocomplete='off' value='" . date("Y-m-d") . "' required>";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='end_date' class='form-label'>Closes after</label>";
    echo "<input type='date' id='end_date' name='end_date' placeholder='End date' class='form-control' autocomplete='off' value='2999-12-31' required>";
    echo "</div>";

    echo "<h4>Site settings</h4>";

    echo "<div class='mb-3'>";
    echo "<label for='title' class='form-label'>Site title</label>";
    echo "<input type='text' class='form-control' id='title' name='title' required />";
    echo "</div>";

    echo "<div class='form-check'>";
    echo "<input class='form-check-input' type='checkbox' name='adminpasswordrequired' id='adminpasswordrequired'>\n";
    echo "<label class='form-check-label' for='adminpasswordrequired' checked='checked'>\n";
    echo "Require a password to get into the admin site (Accounts for James, Steph and Gordon will be created automatically)\n";
    echo "</label>\n";
    echo "</div>\n";

    echo "<div class='mb-3 mt-4'>";
    echo "<label for='homepagetext' class='form-label'>Homepage/instructions text</label>";
    echo "<textarea class='tinymce' name='homepagetext' id='homepagetext'>
    <h2>Like/dislike software</h2>
        <p>Thank you for using our product</p>
        <p>We would be very grateful if you could give us some feedback on it, using the diagram opposite. Just move the mouse to the relevant point, and then click.</p>
        <p>For instance, if you love our product in some ways, but hate it in others, you would click somewhere in the top right. If you don&rsquo;t care much about it either way, you would click somewhere in the bottom left.</p>
        <p>You will then be asked to suggest a way of improving our product.</p>
        <p>Thank you for your help.</p>
    </textarea>";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='topleft' class='form-label'>Top left text</label>";
    echo "<input type='text' class='form-control' value='Love' id='topleft' name='topleft' required />";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='topright' class='form-label'>Top right text</label>";
    echo "<input type='text' class='form-control' value='Love and hate' id='topright' name='topright' required />";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='bottomright' class='form-label'>Bottom right text</label>";
    echo "<input type='text' class='form-control' value='Hate' id='bottomright' name='bottomright' required />";
    echo "</div>";

    echo "<div class='mb-3'>";
    echo "<label for='bottomleft' class='form-label'>Bottom left text</label>";
    echo "<input type='text' class='form-control' value='No feelings either way' id='bottomleft' name='bottomleft' required />";
    echo "</div>";

    echo "<div class='mb-3 row'>";
    echo "<div class='col-2'><a href='/admin/' class='btn btn-primary'>Cancel</a></div>";
    echo "<div class='col-10'><input type='button' class='formsubmit btn btn-primary' id='savesite' disabled='disabled' value='Add Site' /></div>";
    echo "</div>";

    echo "</form>";
}

include("../footer.php");
