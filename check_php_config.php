<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: check_php_config.php
//
// Description: Shows various details on the PHP environment. Whats installed
// and whats not.
//
// Author: Mikael Roos, mos@bth.se
//
// Change history:
// 
//

$html=<<<EOD
<h1>Test a PHP-configuration to check whats installed</h1>
<p>This file is a part of the Utility project which can be downloaded from GitHub.
<p><a href="http://github.com/mosbth/Utility">http://github.com/mosbth/Utility</a></p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Error reporting
//
function error_level_tostring($intval, $separator)
{
    $errorlevels = array(
        2047 => 'E_ALL',
        1024 => 'E_USER_NOTICE',
        512 => 'E_USER_WARNING',
        256 => 'E_USER_ERROR',
        128 => 'E_COMPILE_WARNING',
        64 => 'E_COMPILE_ERROR',
        32 => 'E_CORE_WARNING',
        16 => 'E_CORE_ERROR',
        8 => 'E_NOTICE',
        4 => 'E_PARSE',
        2 => 'E_WARNING',
        1 => 'E_ERROR');
    $result = '';
    foreach($errorlevels as $number => $name)
    {
        if (($intval & $number) == $number) {
            $result .= ($result != '' ? $separator : '').$name; }
    }
    return $result;
}

$html .= "<p>Current level of error-reporting is: " . error_reporting() . "</p>";
$html .= "<p>This means: " .  error_level_tostring(error_reporting(), ' ') . "</p>";


// -------------------------------------------------------------------------------------------
//
// Sessions
//
$html .= "<p>Current length of session is ini_get('session.gc_maxlifetime') : " . ini_get('session.gc_maxlifetime') . "<p>";


// -------------------------------------------------------------------------------------------
//
// Magic quotes
//
$html .= "<p>get_magic_quotes_gpc() is : " . get_magic_quotes_gpc() . "</p>";
$html .= "<p>get_magic_quotes_runtime() is : " . get_magic_quotes_runtime() . "</p>";


// -------------------------------------------------------------------------------------------
//
// PHP version
//
$html .= "<p>phpversion is : " . phpversion() . "</p>";


// -------------------------------------------------------------------------------------------
//
// MySQL 
//
if (function_exists('mysqli_connect')) {
	if(file_exists('config.php')) {
		require_once('config.php');
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		if (mysqli_connect_error()) {
			echo "Connect failed: ".mysqli_connect_error()."<br>";
			//exit();
		}
		
		$html .= "<p>MySQL server is: " . $mysqli->server_info . "</p>";
		$html .= "<p>MySQL client is: " . mysqli_get_client_version() . "</p>";
		$html .= "<p>MySQL protocol is: " . $mysqli->protocol_version . "</p>";
	}	else {
		$html .= "<p>MySQLi is enabled, edit your config.php with connection parameters</p>";
	}
} else {
	$html .= "<p>MySQLi is NOT enabled.</p>";
}


// -------------------------------------------------------------------------------------------
//
// LDAP
//
if (function_exists('ldap_connect')) {
	$html .= "<p>LDAP IS enabled.</p>";
} else {
	$html .= "<p>LDAP is NOT enabled.</p>";
}


// -------------------------------------------------------------------------------------------
//
// Show output from phpinfo() if enabled
//
if(function_exists('sqlite_open')) {
	$html .= "<p style='color:green'>sqlite IS enabled";
} else {
	$html .= "<p style='color:red'>sqlite IS NOT enabled";
}

if(function_exists('sqlite3_open')) {
	$html .= "<p style='color:green'>sqlite3 IS enabled";
} else {
	$html .= "<p style='color:red'>sqlite3 IS NOT enabled";
}

if(class_exists('PDO') && in_array("sqlite", PDO::getAvailableDrivers())) {
	$html .= "<p style='color:green'>sqlite PDO driver IS enabled";
} else {
	$html .= "<p style='color:red'>sqlite PDO driver IS NOT enabled";
}


// -------------------------------------------------------------------------------------------
//
// Show output from phpinfo() if enabled
//
if(isset($_GET['phpinfo'])) {
	echo phpinfo();
	exit;
} else {
	$html .= "<p>phpinfo() might be enabled. <a href='?phpinfo=1'>Click to view</a></p>";
}


// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$title = "Test PHP-configuration, what works and whats not";
$charset = "utf-8";
$language = "en";
 
$html = <<< EOD
<!DOCTYPE html>
<html lang="{$language}">
<head>
	<meta charset="{$charset}" />
	<title>{$title}</title>
 	<style></style>
	<!--[if IE]> 
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>		
	<![endif]-->
</head>
<body>
	{$html}
</body>
</html>
EOD;
 
 
// Print the header and page
header("Content-Type: text/html; charset={$charset}");
echo $html;
exit;


?>