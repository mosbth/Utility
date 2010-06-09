<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: sql_aid.php
//
// Description: Aid to help with SQL related queries. Displays a textarea with a SQL-query.
// There is a permalink to send to someone who can help in correcting the SQL-query.
//
// This is mainly to be used for testing purpose and support to developer who needs assistence
// with SQL-queries.
//
// The script is disabled by default for security reasons.
//
// Author: Mikael Roos, mos@bth.se
//
// Revision history:
//
// 2010-06-09: Refreshed visual output. Tried integrating warnings - failed. Added affected_rows
//             and info(). Enabled changing storagedir using parameter in config.php. Resultset
//             is displayed tablevise.
// 2010-05-06: Initial effort to put it together.
//

// -------------------------------------------------------------------------------------------
//
// Disable by default, change below or in the config.php. Database settings should reside in 
// config.php.
//
error_reporting(E_ALL);
$SQLAID_DISABLED=true;
if(is_readable('config.php')) {	require_once('config.php'); }

if($SQLAID_DISABLED == false) {
	if(!isset($SQLAID_DIRECTORY)) {
		exit("<strong><em>sqlaid is enabled but missing directory to store files. Define \$SQLAID_DIRECTORY.</em></strong>");
	}
	$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . $SQLAID_DIRECTORY;
	if(!(is_dir($dir) && is_writable($dir))) {
		exit("<strong><em>The directory: '{$dir}' does not exists or is not writeable by the webserver.</em></strong>");
	}
} else {
	$dir = '';
}


// -------------------------------------------------------------------------------------------
//
// Get current values for the textareas, from post or stored in file. 
// Save the current value to file.
//
$submit		= isset($_POST['submit']) ? $_POST['submit'] : '';
$case			= isset($_POST['case']) 	? strip_tags($_POST['case']) : (isset($_GET['case']) ? strip_tags($_GET['case']) : '');
$query		= isset($_POST['query']) 	? strip_tags($_POST['query']) : '';

// Fix html encoded issues
$query = str_replace("\'", "'", $query);
$query = str_replace('\"', '"', $query);
$query = str_replace("\\\\", "\\", $query);

$status = "";
// Create new testcase
if($submit == 'new-case') {
	$case = uniqid(basename(__FILE__, '.php'));
	$status = "Created a new case with id={$case}";
	$query = "";
}

// Copy new testcase
if($submit == 'copy-case') {
	$newcase = uniqid(basename(__FILE__, '.php'));
	$r = copy("{$dir}/{$case}.txt", "{$dir}/{$newcase}.txt");
	if($r) {
		$status = "Created a new case with id={$newcase}, copied content from case with id={$case}";
	} else {
		$status = "Failed to copy case id={$case} to new case id={$newcase}";
	}
	$case = $newcase;
}

$filename = "{$dir}/{$case}.txt";

// Write to file
if($submit == 'save-case') {
	file_put_contents($filename, $query) 
		or die("<p>WARNING: FAILED WRITING TO FILE '{$filename}'.<br />Perhaps create tmp-directory and allow webserver to write to it?'</p>");
	$status = "Saved case with id={$case}";
}

// $query is empty, read content from file, if available
if(empty($query) && is_readable($filename)) {
	$query = file_get_contents($filename)
		or die("<p>WARNING: FAILED READING FROM FILE '{$filename}'</p>");
}

// If clear then remove the file, clean up
if($submit == 'remove-case') {
	@unlink($filename);
	$status = "Removed the case with id={$case}";
	$case=""; $query="";
}

// Set status on buttons depending on state
$execute= "";
$save		= "";
$new		= "";
$copy		= "";
$remove	= "";
$link		= "";
if(empty($case)) {
	$execute= "disabled='disabled'";
	$save		= "disabled='disabled'";
	$copy		= "disabled='disabled'";
	$remove	= "disabled='disabled'";
	$link		= "style='display: none;'";
}
if($case == "" . basename(__FILE__, '.php') . "sample") {
	$save		= "disabled='disabled'";
	$remove	= "disabled='disabled'";
}
if(empty($query)) {
	$execute= "disabled='disabled'";
}

// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (mysqli_connect_error()) {
	die("Connect failed: ".mysqli_connect_error()."<br>");
}

$htmlMulti 	= "";

$disabledStatus="";
if($SQLAID_DISABLED) {
	$disabledStatus = "<em>\$SQLAID_DISABLED=true; Change this in the sourcefile or in config.php to enable this script.</em>";
}
else if($submit == 'execute-sql' && !empty($query)) {

	// -------------------------------------------------------------------------------------------
	//
	// Execute query as multi_query
	//
	$res = $mysqli->multi_query($query);
	if($res == false) {
		$htmlMulti .= "Failed querying database.";
	}
	
	//
	// Retrieve and ignore the results from the above query
	// Some may succed and some may fail. Lets count the number of succeded
	// statements to really know.
	//
	$statements = 0;
	do {
		$res = $mysqli->store_result();
		$htmlMulti .= "<p><strong>Statement " . $statements++ . ":</strong><br />";
		$htmlMulti .= "<em>" . $mysqli->affected_rows . " row(s) affected.</em><br />";			
		$htmlMulti .= "<em>" . $mysqli->info . "</em></p>";			

		// Show all warnings
/*
		if ($mysqli->warning_count) { 
			$e = $mysqli->get_warnings(); 
			do { 
				$htmlMulti .=  "Warning: {$e->errno}: {$e->message}<br />"; 
			} while ($e->next()); 
		} 
*/

		if(is_object($res)) {
			$i=1;
			$htmlMulti .= "<table>"; 
			$htmlMulti .= "<caption>Resultset has " . $res->num_rows . " rows.</caption>";
			while ($row = $res->fetch_assoc()) {
				if($i == 1) {
					$htmlMulti .= "<tr><th>Rownum</th>"; 
					foreach($row as $key => $val) {
						$htmlMulti .= "<th>{$key}</th>";
					}
					$htmlMulti .= "</tr>"; 
				}
				$htmlMulti .= "<tr class='r" . ($i % 2 + 1) . "'><td>". $i++ . "</td><td>" . implode('</td><td>', $row) . "</td></tr>";
			}
			$htmlMulti .= "</table>"; 
		}
	} while($mysqli->next_result());

	$htmlMulti .= "<p><strong>Summary</strong><br />";
	$htmlMulti .= "Successful statements: {$statements}<br />";
	$htmlMulti .= "Error code: {$mysqli->errno} ({$mysqli->error})</p>";
}


// -------------------------------------------------------------------------------------------
//
// Prepare the text
//
$script = basename(__FILE__);

$html = <<<EOD
<h1>Create a SQL testcase</h1>
<p>
Create a new testcase and save the query. Send the link of the testcase to a friend and ask for assistance.
</p>
<p>
{$disabledStatus}
</p>
<p>
[<a href='{$script}'>Link to this service</a>] 
[<a href='source.php?dir=&file={$script}'>Sourcecode</a>] 
[<a href='http://github.com/mosbth/Utility/blob/master/sql_aid.php'>GitHub</a>] 
</p>

<fieldset>
<legend><em>Testcase: {$case}</em></legend>
<form action='{$_SERVER['PHP_SELF']}' method='POST'>
<input type='hidden' name='case' value='{$case}'>
<textarea style='width: 100%; white-space: nowrap;' rows='30' name='query'>{$query}</textarea>
<br />
<button {$execute} type='submit' name='submit' value='execute-sql' title='Execute the SQL code and display the result'>Execute SQL</button>
<button {$save} type='submit' name='submit' value='save-case' title='Save the current SQL code to this testcase-file.'>Save testcase</button>
<button {$new} type='submit' name='submit' value='new-case' title='Make a new testcase.'>New testcase</button>
<button {$copy} type='submit' name='submit' value='copy-case' title='Make a new copy of this testcase.'>Copy testcase</button>
<button {$remove} type='submit' name='submit' value='remove-case' title='Remove this testcase.'>Remove testcase</button>
<p><em>{$status}</em></p>
</form>
<p>
[<a {$link} title='Send this link to a friend to share it.' href='{$script}?case={$case}'>Link to this testcase</a>]  
[<a title='View an example of an sample case.' href='{$script}?case=sql_aidsample'>Try a sample testcase</a>] 
</p>
<p>{$htmlMulti}</p>
</fieldset>
EOD;

$html .= "<h2>Details on environment</h2>";
$html .= "<p>PHP is: " . phpversion() . "</p>";
$html .= "<p>MySQL client is: " . mysqli_get_client_version() . "</p>";
$html .= "<p>MySQL protocol is: " . $mysqli->protocol_version . "</p>";
$html .= "<p>MySQL server is: " . $mysqli->server_info . "</p>";

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$title = "SQL aid and assistance";
$charset = "utf-8";
$language = "en";
 
$html = <<< EOD
<!DOCTYPE html>
<html lang="{$language}">
<head>
<meta charset="{$charset}" />
<title>{$title}</title>
<style>
table th {
	background: #aaa;
}
table tr.r1 {
	background: #ddd;
}
table tr.r2 {
	background: #eee;
}
table td {
	background: inherit;
}
table caption {
	float: left;
	font-style: italic;
}
</style>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
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
