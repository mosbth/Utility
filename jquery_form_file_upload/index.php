<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: jquery_form_file_upload/index.php
//
// Description: Example code on using file upload with jquery form plugin.
//
// Author: Mikael Roos, mos@bth.se
//
// Change history:
// 
//

// -------------------------------------------------------------------------------------------
//
// Settings for this pagecontroller. Review and change these settings to match your own
// environment.
//
error_reporting(E_ALL);
//$LDAP_DISABLED=true;
if(is_readable('config.php')) {	require_once('config.php'); }


// -------------------------------------------------------------------------------------------
//
// Create the JavaScript
//
$javascript = <<<EOD
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://dev.phpersia.org/persia/js/form/jquery.form.js"></script>
<script type="text/javascript" src="script.js"></script>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create the html
//
$html = <<<EOD

<form id="uploadForm" action="process.php" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />

			File: <input type="file" name="file" />
			Return Type: <select id="uploadResponseType" name="mimetype">
					<option value="html">html</option>
					<option value="json">json</option>
					<option value="script">script</option>
					<option value="xml">xml</option>

			</select>
			<input type="submit" value="Submit" />
	</form>

	<p />
	<label>Output:</label>
	<div id="uploadOutput"></div>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$title = "Examples on jQuery forms plugin and file upload";
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
{$javascript}
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