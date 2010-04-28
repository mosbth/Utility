<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: ldap.php
//
// Description: Example code on LDAP.
//
// Author: Mikael Roos, mos@bth.se
//
// Change history:
// 
// 2010-04-27: 
// First version.
//

// -------------------------------------------------------------------------------------------
//
// Settings for this pagecontroller. Review and change these settings to match your own
// environment.
//


// -------------------------------------------------------------------------------------------
//
// Get the input variables from GET.
//
$submit = $_GET['submit'];
$server = $_GET['server'];
$server = $_GET['basedn'];
$server = $_GET['uid'];


// -------------------------------------------------------------------------------------------
//
// Do some action depending on whats submitted. 
//

// -------------------------------------------------------------------------------------------
//
// Connect to a LDAP server, display error if failing.
//
if($submit == 'connect-to-server') {

	$connectStatus = "<p>Connecting... ";
	$ds=ldap_connect($server);
	$connectStatus .= "done. Result is '{$ds}'.</p>";
  ldap_close($ds);
}


// -------------------------------------------------------------------------------------------
//
// Connect to a LDAP server, display error if failing.
//
else if($submit == 'bind-to-server') {

	$bindStatus = "<p>Connecting and binding... ";
	$ds=ldap_connect($server);
  $r=ldap_bind($ds);
	$bindStatus .= "done. Bind result is '{$r}'.</p>";
  ldap_close($ds);
}


// -------------------------------------------------------------------------------------------
//
// Searching for entries with specified uid in LDAP server, display error if failing.
//
else if($submit == 'search-uid') {

	$searchStatus = "<p>Connecting and binding... ";
	$ds=ldap_connect($server);
  $r=ldap_bind($ds);
	$searchStatus .= "done. Bind result is '{$r}'.</p>";
	$searchStatus .= "<p>Searching for 'uid={$uid}'...";
	$sr=ldap_search($ds, $basedn, "uid={$uid}");
	$searchStatus .= "done.<br /> Result is '{$sr}'.<br />";
	$searchStatus .= "Number of entries returned is '" . ldap_count_entries($ds, $sr) . "'<br />";
	$searchStatus .= "</p>";
	
/*
echo "Getting entries ...<p>";
    $info = ldap_get_entries($ds, $sr);
    echo "Data for " . $info["count"] . " items returned:<p>";

    for ($i=0; $i<$info["count"]; $i++) {
        echo "dn is: " . $info[$i]["dn"] . "<br />";
        echo "first cn entry is: " . $info[$i]["cn"][0] . "<br />";
        echo "first email entry is: " . $info[$i]["mail"][0] . "<br /><hr />";
    }
*/

  ldap_close($ds);
}


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$html = <<<EOD
<h1>Various examples on PHP and LDAP</h1>
<p>
--
</p>

<h2 id='connect'>Connecting to an LDAP-server</h2>
<form action='{$_SERVER['PHP_SELF']}' method='GET'>
<fieldset>
<table width='600px'>
<tr>
<td><label for="server">LDAP-server:</label></td>
<td style='text-align: right;'><input type='text' name='server' value='{$server}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='connect-to-server'>Connect to server</button>
</td>
</tr>
</table>
<p>
{$connectStatus}
</p>
</fieldset>
</form>

<h2 id='bind'>Connect and bind to an LDAP-server</h2>
<form action='{$_SERVER['PHP_SELF']}' method='GET'>
<fieldset>
<table width='600px'>
<tr>
<td><label for="server">LDAP-server:</label></td>
<td style='text-align: right;'><input type='text' name='server' value='{$server}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='bind-to-server'>Bind to server</button>
</td>
</tr>
</table>
<p>
{$bindStatus}
</p>
</fieldset>
</form>

<h2 id='search'>Search using "uid=..."</h2>
<form action='{$_SERVER['PHP_SELF']}' method='GET'>
<fieldset>
<table width='600px'>
<tr>
<td><label for="server">LDAP-server:</label></td>
<td style='text-align: right;'><input type='text' name='server' value='{$server}'></td>
</tr>
<tr>
<td><label for="basedn">Base DN (Distinguished Name):</label></td>
<td style='text-align: right;'><input type='text' name='basedn' value='{$dn}'></td>
</tr>
<tr>
<td><label for="uid">User id (uid):</label></td>
<td style='text-align: right;'><input type='text' name='uid' value='{$uid}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='search-uid'>Search server</button>
</td>
</tr>
</table>
<p>
{$bindStatus}
</p>
</fieldset>
</form>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$title = "Examples on PHP and LDAP";
$charset = "utf-8";
$language = "en";
 
$html = <<< EOD
<!DOCTYPE html>
<html lang="{$language}">
<head>
	<meta charset="{$charset}" />
	<title>{$title}</title>
 	<style>
 		fieldset {
			width: 650px;
		}

 		label {
			font-family: Verdana, Sans-serif;
			font-size: 1.2em;
		}

 		input {
			font-family: Verdana, Sans-serif;
			width: 24em;
			font-size: 1.2em;
			padding: 0.3em;
		}

		button {
			font-family: Verdana, Sans-serif;
			width: 12em;	
			font-size: 1.2em;
			padding: 0.3em;
			margin-top: 0.5em;
		}
	</style>
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