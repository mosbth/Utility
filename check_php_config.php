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

$html="";

// -------------------------------------------------------------------------------------------
//
// Error reporting
//
$html .= "<p>Current level of error-reporting is: " . error_reporting() . "</p>";


// -------------------------------------------------------------------------------------------
//
// Sessions
//
$html .= "<p>Current length of session is ini_get('session.gc_maxlifetime') : " . ini_get('session.gc_maxlifetime') . ".<p>";


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
			exit();
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