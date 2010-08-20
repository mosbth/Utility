<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: ldap.php
//
// Description: Example code on LDAP.
//
// The file is disabled by default for security reasons.
//
// Author: Mikael Roos, mos@bth.se
//
// Change history:
// 
// 2010-06-10: Support TLS by using:
// 	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
//	ldap_start_tls($ds);
//
// 2010-04-27: First version.
//
//

// -------------------------------------------------------------------------------------------
//
// Settings for this pagecontroller. Review and change these settings to match your own
// environment.
//
error_reporting(E_ALL);
$LDAP_DISABLED=true;
if(is_readable('config.php')) {	require_once('config.php'); }


// -------------------------------------------------------------------------------------------
//
// Function to read entries from LDAP. Return results as string.
//	
function get_entries($ds, $sr) {

	$res = "<p>Getting entries...";
	$info = ldap_get_entries($ds, $sr);
  $res .= "done. Data for '{$info['count']}' items returned:</p>";

	for ($i=0; $i<$info["count"]; $i++) {
		$res .= "<p>";
		$res .= "dn: {$info[$i]['dn']} ({$info[$i]['count']} )<br />";
		@$res .= "cn[0]: {$info[$i]['cn'][0]} <br />";
		$res .= "sn[0]: {$info[$i]['sn'][0]}<br />";
		$res .= "uid[0]: {$info[$i]['uid'][0]}<br />";
		$res .= "mail[0]: {$info[$i]['mail'][0]}<br />";
		$res .= "<p>";
		//echo "<pre>"; print_r($info[$i]); echo "</pre>";
	}
	return $res;
}


// -------------------------------------------------------------------------------------------
//
// Function to escape special characters when using LDAP.
// Got it from the PHP manual in the user comments.
// http://www.php.net/manual/en/function.ldap-search.php#90158
//
function ldap_escape($str, $for_dn = false) {
    // see:
    // RFC2254
    // http://msdn.microsoft.com/en-us/library/ms675768(VS.85).aspx
    // http://www-03.ibm.com/systems/i/software/ldap/underdn.html       
       
    if  ($for_dn)
        $metaChars = array(',','=', '+', '<','>',';', '\\', '"', '#');
    else
        $metaChars = array('*', '(', ')', '\\', chr(0));

    $quotedMetaChars = array();
    foreach ($metaChars as $key => $value) $quotedMetaChars[$key] = '\\'.str_pad(dechex(ord($value)), 2, '0');
    $str=str_replace($metaChars,$quotedMetaChars,$str); //replace them
    return ($str);
} 


// -------------------------------------------------------------------------------------------
//
// Get the input variables from POST and check/escape them properly.
//
$submit 	= strip_tags(isset($_POST['submit']) ? $_POST['submit'] : '');
$server 	= strip_tags(isset($_POST['server']) ? $_POST['server']: '');
$basedn 	= ldap_escape(isset($_POST['basedn']) ? $_POST['basedn'] : '', false);
$uid			= strip_tags(isset($_POST['uid']) ? $_POST['uid'] : '');
$password	= strip_tags(ldap_escape(isset($_POST['password']) ? urldecode($_POST['password']) : ''));
$usetls		= strip_tags(isset($_POST['tls']) ? $_POST['tls'] : '');

//$basedn 	= strip_tags($_POST['basedn']);
//$uid			= strip_tags($_POST['uid']);
//$password	= strip_tags($_POST['password']);

$disabledStatus = "";
$connectStatus 	= "";
$bindStatus 		= "";
$searchStatus 	= "";
$passwordStatus = "";

echo $_POST['password'] . "<br>" . $password;



// -------------------------------------------------------------------------------------------
//
// Do some action depending on whats submitted. 
//

if($LDAP_DISABLED) {
	$disabledStatus = "\$LDAP_DISABLED=true; Change this in the sourcefile or in config.php to enable this script.";
}


// -------------------------------------------------------------------------------------------
//
// Connect to a LDAP server, display error if failing.
//
else if($submit == 'connect-to-server') {

	$connectStatus = "<p>Connecting... ";
	$ds = ldap_connect($server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	if($usetls) {
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
		ldap_start_tls($ds);
	}
	$connectStatus .= "done. Result is '{$ds}'.</p>";
  ldap_close($ds);
}


// -------------------------------------------------------------------------------------------
//
// Connect to a LDAP server, display error if failing.
//
else if($submit == 'bind-to-server') {

	$bindStatus = "<p>Connecting and binding... ";
	$ds	= ldap_connect($server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	if($usetls) {
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
		ldap_start_tls($ds);
	}
	$r	= ldap_bind($ds);
	$bindStatus .= "done. Bind result is '{$r}'.</p>";
  ldap_close($ds);
}


// -------------------------------------------------------------------------------------------
//
// Searching for entries with specified uid in LDAP server, display error if failing.
//
else if($submit == 'search-uid') {

	$searchStatus = "<p>Connecting and binding... ";
	$ds	= ldap_connect($server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	if($usetls) {
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
		ldap_start_tls($ds);
	}
  $r	= ldap_bind($ds);
	$searchStatus .= "done. Bind result is '{$r}'.</p>";
	$searchStatus .= "<p>Searching for 'uid={$uid}'...";
	$sr = ldap_search($ds, $basedn, "uid={$uid}");
	$searchStatus .= "done.<br /> Result is '{$sr}'.<br />";
	$searchStatus .= "Number of entries returned is '" . ldap_count_entries($ds, $sr) . "'<br />";
	$searchStatus .= "</p>";
	$searchStatus .= get_entries($ds, $sr);
  ldap_close($ds);
}


// -------------------------------------------------------------------------------------------
//
// Checking an uid to see if the password match, authentication of user.
//
else if($submit == 'check-password') {

	$passwordStatus = "<p>Connecting and binding... ";
	$ds	= ldap_connect($server);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	if($usetls) {
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
		ldap_start_tls($ds);
	}
  $r	= ldap_bind($ds);
	$passwordStatus .= "done. Bind result is '{$r}'.</p>";
	$passwordStatus .= "<p>Searching for 'uid={$uid}'...";
	$sr	= ldap_search($ds, $basedn, "uid={$uid}");
	$passwordStatus .= "done.<br /> Result is '{$sr}'.<br />";
	$passwordStatus .= "Number of entries returned is '" . ldap_count_entries($ds, $sr) . "'<br />";
	$passwordStatus .= "</p>";
	$passwordStatus .= "<p>Binding using dn and password...";
	$info	=	ldap_get_entries($ds, $sr);
	$r		=	@ldap_bind($ds, $info[0]['dn'], $password);
  $passwordStatus .= "done. Result is '{$r}'. User IS " . ($r && $password != '' ? '' : 'NOT') . " authenticated.</p>";
	$passwordStatus .= get_entries($ds, $sr);
  ldap_close($ds);
}


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$script = basename(__FILE__);

$tlschecked = (!empty($usetls)) ? 'checked' : '';

$html = <<<EOD
<h1>Various examples on PHP and LDAP</h1>
<p>
Shows how to use PHP and LDAP to communicate with a LDAP server. 
</p>
<p>
{$disabledStatus}
</p>
<p>
[<a href='{$script}'>Link to this service</a>] 
[<a href='source.php?dir=&file={$script}'>Sourcecode</a>] 
[<a href='http://github.com/mosbth/Utility/blob/master/ldap.php'>GitHub</a>] 
</p>

<h2 id='connect'>Connecting to an LDAP-server</h2>
<form action='{$_SERVER['PHP_SELF']}' method='POST'>
<fieldset>
<table width='600px'>
<tr>
<td><label for="server">LDAP-server:</label></td>
<td style='text-align: right;'><input type='text' name='server' value='{$server}'></td>
</tr>
<tr>
<td><label for="tls">Use TLS:</label></td>
<td><input type='checkbox' name='tls' value='on' {$tlschecked}></td>
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
<form action='{$_SERVER['PHP_SELF']}' method='POST'>
<fieldset>
<table width='600px'>
<tr>
<td><label for="server">LDAP-server:</label></td>
<td style='text-align: right;'><input type='text' name='server' value='{$server}'></td>
</tr>
<tr>
<td><label for="tls">Use TLS:</label></td>
<td><input type='checkbox' name='tls' value='on' {$tlschecked}></td>
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
<form action='{$_SERVER['PHP_SELF']}' method='POST'>
<fieldset>
<table width='600px'>
<tr>
<td><label for="server">LDAP-server:</label></td>
<td style='text-align: right;'><input type='text' name='server' value='{$server}'></td>
</tr>
<tr>
<td><label for="tls">Use TLS:</label></td>
<td><input type='checkbox' name='tls' value='on' {$tlschecked}></td>
</tr>
<tr>
<td><label for="basedn">Base DN (Distinguished Name):</label></td>
<td style='text-align: right;'><input type='text' name='basedn' value='{$basedn}'></td>
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
{$searchStatus}
</p>
</fieldset>
</form>

<h2 id='search'>Authenticate user</h2>
<form action='{$_SERVER['PHP_SELF']}' method='POST'>
<fieldset>
<table width='600px'>
<tr>
<td><label for="server">LDAP-server:</label></td>
<td style='text-align: right;'><input type='text' name='server' value='{$server}'></td>
</tr>
<tr>
<td><label for="tls">Use TLS:</label></td>
<td><input type='checkbox' name='tls' value='on' {$tlschecked}></td>
</tr>
<tr>
<td><label for="basedn">Base DN (Distinguished Name):</label></td>
<td style='text-align: right;'><input type='text' name='basedn' value='{$basedn}'></td>
</tr>
<tr>
<td><label for="uid">User id (uid):</label></td>
<td style='text-align: right;'><input type='text' name='uid' value='{$uid}'></td>
</tr>
<tr>
<td><label for="password">Password:</label></td>
<td style='text-align: right;'><input type='password' name='password' value='{$password}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='check-password'>Check password</button>
</td>
</tr>
</table>
<p>
{$passwordStatus}
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