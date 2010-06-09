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

// -------------------------------------------------------------------------------------------
//
// Show output from phpinfo() if enabled
//
if(phpinfo()) {
	$html = phpinfo();
} else {
	$html = "<p>phpinfo() is disabled.</p>"
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