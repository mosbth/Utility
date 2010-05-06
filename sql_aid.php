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
// The file is disabled by default for security reasons.
//
// Author: Mikael Roos, mos@bth.se
//

// -------------------------------------------------------------------------------------------
//
// Settings for this pagecontroller. Review and change these settings to match your own
// environment.
//
error_reporting(E_ALL);
$SQLAID_DISABLED=true;
if(is_readable('config.php')) {	require_once('config.php'); }


// -------------------------------------------------------------------------------------------
//
// Get current values for the textareas, from post or stored in file. 
// Save the current value to file.
//
$submit		= isset($_POST['submit']) ? $_POST['submit'] : '';
$query		= isset($_POST['query']) ? $_POST['query'] : '';
$clear		= isset($_GET['clear']);
$filename = "tmp/" . basename(__FILE__, '.php') . '.txt';

// Fix html encoded issues
$query = str_replace("\'", "'", $query);
$query = str_replace('\"', '"', $query);
$query = str_replace("\\\\", "\\", $query);

// Write to file
if($submit == 'save-query') {
	file_put_contents($filename, $query) 
		or die("<p>WARNING: FAILED WRITING TO FILE '{$filename}'.<br />Perhaps create tmp-directory and allow webserver to write to it?'</p>");
}

// $query is empty, read content from file, if available
if(empty($query) && is_readable($filename)) {
	$query = file_get_contents($filename)
		or die("<p>WARNING: FAILED READING FROM FILE '{$filename}'</p>");
}

// If clear then remove the file, clean up
if($clear && is_readable($filename)) {
	unlink($filename);
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
$htmlSingle = "";

if($submit == 'execute-sql' && !empty($query)) {

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
		$htmlMulti .= "<p>Statement " . $statements++ . ":</p>";
		if(is_object($res)) {
			$htmlMulti .= "Resultset has " . $res->num_rows . " rows.<pre>";
			while ($row = $res->fetch_assoc()) {
				$htmlMulti .= "" . implode(', ', $row) . "\n";
			}
			$htmlMulti .= "</pre>";
		}		
	} while($mysqli->next_result());

	$htmlMulti .= "<p>Successful statements: {$statements}</p>";
	$htmlMulti .= "<p>Error code: {$mysqli->errno} ({$mysqli->error})</p>";

/*
	// -------------------------------------------------------------------------------------------
	//
	// Execute query as several queries
	//
	$queries = explode(';', $query);

	$htmlSingle .= "<h2>Testcase 2: explode() &amp; query()</h2>";
	$statements = 0;
	foreach($queries as $val) {
		if(empty($val)) break;
		$res = $mysqli->query($val);
		$statements += (empty($res) ? 0 : 1);
		$htmlSingle .= "<p><hr>Query:<pre>{$val}</pre>Results: {$res}</p>";
	}

	$htmlSingle .= "<p>Antal lyckade statements: {$statements}</p>";
	$htmlSingle .= "<p>Error code: {$mysqli->errno} ({$mysqli->error})</p>";
*/
}


// -------------------------------------------------------------------------------------------
//
// Prepare the text
//
$script = basename(__FILE__);

$html = <<<EOD
<h1>Create a SQL testcase</h1>
<p>
Create a new testcase and save the query. Send the link to a friend and ask for assistance.
</p>
<p>
[<a href='{$script}'>Link to this service</a>] 
[<a href='source.php?dir=&file={$script}'>Sourcecode</a>] 
</p>

<fieldset><legend><em>Testcase: </em></legend>
<form action='{$_SERVER['PHP_SELF']}' method='POST'>
<p>
[<a href='{$script}'>Link to this testcase</a>] 
[<a href='{$script}?clear=clear'>Remove this testcase</a>] 
</p>
<textarea cols='80' rows='20' name='query'>{$query}</textarea>
<br />
<button type='submit' name='submit' value='execute-sql' title='Execute the SQL code and display the result'>Execute SQL</button>
<button type='submit' name='submit' value='save-sql' title='Save the current SQL code to this testcase-file.'>Save SQL</button>
</form>
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
$title = "SQL aid and assitance";
$charset = "utf-8";
$language = "en";
 
$html = <<< EOD
<!DOCTYPE html>
<html lang="{$language}">
<head>
<meta charset="{$charset}" />
<title>{$title}</title>
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
