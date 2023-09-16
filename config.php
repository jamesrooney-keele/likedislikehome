<?php

session_start();

include_once(__DIR__ . "/dbconfig.php");

include_once(__DIR__ . "/includes.php");
include(__DIR__ . "/www/vendor/autoload.php");

$mysqli = new mysqli(MYSQL_HOST,MYSQL_USER, trim(file_get_contents(__DIR__.'/dbpasswd.txt')),MYSQL_DATABASE);

try{
	$dbh = new PDO("mysql:host=localhost;port=3306;dbname=".MYSQL_DATABASE, MYSQL_USER, trim(file_get_contents(__DIR__.'/dbpasswd.txt')));
}catch (Exception $e){
	echo "Unable to connect: " . $e->getMessage() ."<p>";
}