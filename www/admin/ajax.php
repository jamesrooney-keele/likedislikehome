<?php
require_once("authorise.php");

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {

        case 'checkemailfordupes':

            $output = array();

            $email = $_POST['email'];
            $userid = $_POST['userid'];

            $found = checkEmailForDupes($email, $userid);

            if ($found) {
                echo "1";
            } else {
                echo "0";
            }

            $location = false;
            break;

        case 'checkurlfordupes':

            $output = array();

            $url = $_POST['url'];
            $siteid = $_POST['siteid'];

            $found = checkSiteForDupes($url, $siteid);

            if ($found) {
                echo "1";
            } else {
                echo "0";
            }

            $location = false;
            break;
    }
}
