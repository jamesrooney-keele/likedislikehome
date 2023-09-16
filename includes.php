<?php

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

#Get a random alphanumeric string.	For use with passwords and encryption keys
function rand_string($length)
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	$str = "";
	$size = strlen($chars);
	for ($i = 0; $i < $length; $i++) {
		$str .= $chars[rand(0, $size - 1)];
	}

	return $str;
}

function rand_color()
{
	return str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}

function stripHTMLForSearch($html)
{
	return strip_tags(preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $html));
}

function replace_links($text)
{
	$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);

	$ret = ' ' . $text;

	// Replace Links with http://
	$ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);

	// Replace Links without http://
	$ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);

	// Replace Email Addresses
	$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
	$ret = substr($ret, 1);

	return $ret;
}

/**
 * "Liberated" from https://stackoverflow.com/questions/2050859/copy-entire-contents-of-a-directory-to-another-using-php
 */
function recurseCopy(
	string $sourceDirectory,
	string $destinationDirectory,
	string $childFolder = ''
): void {
	$directory = opendir($sourceDirectory);

	if (is_dir($destinationDirectory) === false) {
		mkdir($destinationDirectory);
	}

	if ($childFolder !== '') {
		if (is_dir("$destinationDirectory/$childFolder") === false) {
			mkdir("$destinationDirectory/$childFolder");
		}

		while (($file = readdir($directory)) !== false) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			if (is_dir("$sourceDirectory/$file") === true) {
				recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
			} else {
				copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
			}
		}

		closedir($directory);

		return;
	}

	while (($file = readdir($directory)) !== false) {
		if ($file === '.' || $file === '..') {
			continue;
		}

		if (is_dir("$sourceDirectory/$file") === true) {
			recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
		} else {
			copy("$sourceDirectory/$file", "$destinationDirectory/$file");
		}
	}

	closedir($directory);
}

function showPlural($num)
{
	if ($num > 1) {
		return "s";
	} else {
		return "";
	}
}

function sendEmail($to, $subject, $body, $attachments = array(), $keytype = null, $keyid = null)
{

	$mail = new PHPMailer(true);
	try {
		$mail->CharSet = 'UTF-8';

		$mail->setFrom("noreply@likedislike.co.uk");

		$attachmentslist = array();
		$attachmentfullpathlist = array();
		foreach ($attachments as $attachment) {
			if (file_exists($attachment)) {
				$mail->addAttachment($attachment);
				$patharray = explode("/", $attachment);
				$attachmentslist[] = end($patharray);
				$attachmentfullpathlist[] = $attachment;
			}
		}
		$attachmentstring = implode(",", $attachmentslist);
		$attachmentfullpathstring = implode(",", $attachmentfullpathlist);

		$splitto = explode(",", $to);
		foreach ($splitto as $thisto) {
			$mail->addAddress($thisto);
		}
		$mail->addBCC("astropointier@gmail.com"); #debug option
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->send();

		return true;
	} catch (Exception $e) {

		return $e->getMessage();
	}
}

// original code: http://www.daveperrett.com/articles/2008/03/11/format-json-with-php/
// adapted to allow native functionality in php version >= 5.4.0
// https://github.com/GerHobbelt/nicejson-php/blob/master/nicejson.php
/**
 * Format a flat JSON string to make it more human-readable
 *
 * @param string $json The original JSON string to process
 *        When the input is not a string it is assumed the input is RAW
 *        and should be converted to JSON first of all.
 * @return string Indented version of the original JSON string
 */
function json_format($json)
{
	if (!is_string($json)) {
		if (phpversion() && phpversion() >= 5.4) {
			return json_encode($json, JSON_PRETTY_PRINT);
		}
		$json = json_encode($json);
	}
	$result      = '';
	$pos         = 0;               // indentation level
	$strLen      = strlen($json);
	$indentStr   = "\t";
	$newLine     = "\n";
	$prevChar    = '';
	$outOfQuotes = true;

	for ($i = 0; $i < $strLen; $i++) {
		// Speedup: copy blocks of input which don't matter re string detection and formatting.
		$copyLen = strcspn($json, $outOfQuotes ? " \t\r\n\",:[{}]" : "\\\"", $i);
		if ($copyLen >= 1) {
			$copyStr = substr($json, $i, $copyLen);
			// Also reset the tracker for escapes: we won't be hitting any right now
			// and the next round is the first time an 'escape' character can be seen again at the input.
			$prevChar = '';
			$result .= $copyStr;
			$i += $copyLen - 1;      // correct for the for(;;) loop
			continue;
		}

		// Grab the next character in the string
		$char = substr($json, $i, 1);

		// Are we inside a quoted string encountering an escape sequence?
		if (!$outOfQuotes && $prevChar === '\\') {
			// Add the escaped character to the result string and ignore it for the string enter/exit detection:
			$result .= $char;
			$prevChar = '';
			continue;
		}
		// Are we entering/exiting a quoted string?
		if ($char === '"' && $prevChar !== '\\') {
			$outOfQuotes = !$outOfQuotes;
		}
		// If this character is the end of an element,
		// output a new line and indent the next line
		else if ($outOfQuotes && ($char === '}' || $char === ']')) {
			$result .= $newLine;
			$pos--;
			for ($j = 0; $j < $pos; $j++) {
				$result .= $indentStr;
			}
		}
		// eat all non-essential whitespace in the input as we do our own here and it would only mess up our process
		else if ($outOfQuotes && false !== strpos(" \t\r\n", $char)) {
			continue;
		}

		// Add the character to the result string
		$result .= $char;
		// always add a space after a field colon:
		if ($outOfQuotes && $char === ':') {
			$result .= ' ';
		}

		// If the last character was the beginning of an element,
		// output a new line and indent the next line
		else if ($outOfQuotes && ($char === ',' || $char === '{' || $char === '[')) {
			$result .= $newLine;
			if ($char === '{' || $char === '[') {
				$pos++;
			}
			for ($j = 0; $j < $pos; $j++) {
				$result .= $indentStr;
			}
		}
		$prevChar = $char;
	}

	return $result;
}

/**
 * Calls a MySQL prepared statement via PDO
 * query: The query with ':varname' for the variables to be filled in
 * params: An array of parameters. Each parameter is a key => value pair as $varname => $value, e.g. ':varname' => "Test"
 * flags: Array of specific flags to use.
 */
function pdoCall($query, $params = array(), $flags = array(), $returnfullhandler = false)
{
	global $dbh;

	// Default flag values
	if (!isset($flags['buffered'])) $flags['buffered'] = true;

	try {
		if (!$flags['buffered']) $dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
		$stmt = $dbh->prepare($query);
		$res  = $stmt->execute($params);
		if (!$flags['buffered']) $dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
	} catch (\Exception $e) {
		pdoError($e, $query, $params, $e->getTraceAsString());
		error_log("PDO Backtrace: " . $e->getTraceAsString());

		throw $e;
	}

	if (!$res) {
		echo "<p>Unable to execute statement: " . $stmt->errorInfo()[2] . "</p>";
		pdoError($stmt, $query, $params, $stmt->errorInfo()[2]);
		return false;
	}

	if ($returnfullhandler || strpos($query, "INSERT INTO") === false) {
		return $stmt;
	} else {
		//if an insert query, return the row ID as we should never need to loop through the results
		$rowid = $dbh->lastInsertId();
		if (is_numeric($rowid) && $rowid > 0) {
			return $rowid;
		} else {
			return $stmt;
		}
	}
}

/**
 * Log a database error
 * @param $errorMsg
 * @param $query
 * @param $params
 */
function pdoError($errorTrace, $query, $params, $errorMsg)
{

	$subject = "SQL ERROR at " . $_SERVER['SERVER_NAME'];
	$body = "Statement: $query";
	$parambody = "";

	error_log("PDO Error Code: " . $errorMsg . ". Error Msg (if set)" . $errorMsg);
	error_log("PDO SQL: " . $query);
	if ($params) {
		foreach ($params as $key => $value) {
			error_log("PDO Param: {$key} => {$value}");
			$parambody .= "$key,$value<br>";
		}
	}
	$body .= "<p>$parambody</p>";
	$body .= "<p>Script filename: {$_SERVER['SCRIPT_FILENAME']}</p>";
	$body .= "<p>URL: {$_SERVER['REQUEST_URI']}</p>";
	$body .= "<p>Error: " . var_export($errorTrace, true) . "</p>";

	try {
		$a = sendEmail("james_rooney@hotmail.com", $subject, $body);
	} catch (\Exception $e) {
		error_log("Unable to send warning email");
	}
}



function getUserByEmail($email){
	global $siteid;

	$statement = "SELECT * FROM user WHERE siteid IS NULL AND email = :email";
	$params = array();
	$params[':email'] = $email;
	$sth = pdoCall($statement, $params);
	while($result = $sth->fetch(PDO::FETCH_ASSOC)){
		return $result;
	}

	return false;
}

function getUserById($userid){
	global $siteid;

	$statement = "SELECT * FROM user WHERE siteid IS NULL AND id = :userid";
	$params = array();
	$params[':userid'] = $userid;
	$sth = pdoCall($statement, $params);
	while($result = $sth->fetch(PDO::FETCH_ASSOC)){
		return $result;
	}

	return false;
}

function checkEmailForDupes($email, $userid)
{
	global $siteid;

	$alreadyfound = false;
	$statement = "SELECT id FROM user WHERE siteid IS NULL AND email = :email";
	if ($userid != -1) {
		$statement .= " AND id != :userid";
	}
	$params = array();
	$params[':email'] = $email;
	if ($userid != -1) {
		$params[':userid'] = $userid;
	}

	$sth = pdoCall($statement, $params);
	while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
		$alreadyfound = true;
	}

	return $alreadyfound;
}

function getUsers($includedeleted = false)
{
	global $siteid;

	$statement = "SELECT * FROM user WHERE siteid IS NULL";
	if (!$includedeleted) {
		$statement .= " AND enabled = 0";
	}
	$users = array();
	$params = array();
	$sth = pdoCall($statement, $params);
	while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
		$users[$result['id']] = $result;
	}
	return $users;
}

function getSiteById($siteid){

	$statement = "SELECT * FROM sites WHERE id = :siteid";
	$params = array();
	$params[':siteid'] = $siteid;
	$sth = pdoCall($statement, $params);
	while($result = $sth->fetch(PDO::FETCH_ASSOC)){
		return $result;
	}

	return false;
}

function getSiteByURL($url){

	$statement = "SELECT * FROM sites WHERE url = :url";
	$params = array();
	$params[':url'] = $url;
	$sth = pdoCall($statement, $params);
	while($result = $sth->fetch(PDO::FETCH_ASSOC)){
		return $result;
	}

	return false;
}

function checkSiteForDupes($url, $siteid){

	$alreadyfound = false;
	$params = array();
	$params[':url'] = $url ;
	$statement = "SELECT id,url FROM sites WHERE url = :url ";
	if ($siteid != -1) {
		$statement .= " AND id != :siteid";
		$params[':siteid'] = $siteid;
	}
	$sth = pdoCall($statement, $params);
	while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
		$alreadyfound = true;
	}

	return $alreadyfound;

}
?>