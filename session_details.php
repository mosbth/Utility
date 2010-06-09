<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: session_details.php
//
// Description: Check and set details about session. 
//
// Author: Mikael Roos, mos@bth.se
//
// Revision history:
//
// 2010-05-17: Initial effort to put it together.
//


// -------------------------------------------------------------------------------------------
//
// Print out details of the current session
//

$html = <<<EOD
<h1>nnn</h1>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$title = "Session details";
$charset = "utf-8";
$language = "en";
 
$html = <<< EOD
<!DOCTYPE html>
<html lang="{$language}">
<head>
<meta charset="{$charset}" />
<title>{$title}</title>
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
