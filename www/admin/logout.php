<?php

include_once(__DIR__ ."/../../config.php");

session_regenerate_id();
session_destroy();

setcookie("userid", "", time()-1, '/', $_SERVER['SERVER_NAME']);
setcookie("Email", "", time()-1, '/', $_SERVER['SERVER_NAME']);
setcookie("CheckID", "", time()-1, '/', $_SERVER['SERVER_NAME']);

header("Location: .");
?>
