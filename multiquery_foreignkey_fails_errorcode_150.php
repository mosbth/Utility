<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: multiquery_foreignkey_fails_errorcode_150.php
//
// Description: Common file for test purpose. Easy to use to test database connections, depends on 
// config.php where all database details are. 
//
// This particular testcase is to verify if there is a problem with creating tables
// in a multi_query with foreign key constraints.
//
// Author: Mikael Roos, mos@bth.se
//

require_once('config.php');


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (mysqli_connect_error()) {
   echo "Connect failed: ".mysqli_connect_error()."<br>";
   exit();
}


// -------------------------------------------------------------------------------------------
//
// Get values from _GET to enable multiple testcases using same source
//
$engine = (isset($_GET['engine'])) ? $_GET['engine'] : 'myisam';

$storageEngine = "";
switch($engine) {
	case 'innodb': $storageEngine = 'Engine=InnoDB'; break;
	case 'myisam': 
	default: $storageEngine = 'Engine=MyISAM'; break;
}


// -------------------------------------------------------------------------------------------
//
// Prepare the SQL query.
//
$tableProfessor = 'test_professor';
$tableGrade     = 'test_grade';

$query = <<<EOD
DROP TABLE IF EXISTS {$tableGrade};
DROP TABLE IF EXISTS {$tableProfessor};

CREATE TABLE {$tableProfessor} (
	idProfessor INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY
) {$storageEngine};

CREATE TABLE {$tableGrade} (
	idGrade INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	Grade_idProfessor INT UNSIGNED NOT NULL,
	FOREIGN KEY (Grade_idProfessor) REFERENCES {$tableProfessor} (idProfessor)
) {$storageEngine};
EOD;


// -------------------------------------------------------------------------------------------
//
// Execute query as multi_query
//
$res = $mysqli->multi_query($query)
                    or die("Could not query database");

//
// Retrieve and ignore the results from the above query
// Some may succed and some may fail. Lets count the number of succeded
// statements to really know.
//
$statements = 0;
do {
	$res = $mysqli->store_result();
	$statements++;
} while($mysqli->next_result());

$htmlMulti  = "<h2>Testcase 1: multi_query()</h2>";
$htmlMulti .= "<p>Query=<br/><pre>{$query}</pre></p>";
$htmlMulti .= "<p>Antal lyckade statements: {$statements}</p>";
$htmlMulti .= "<p>Error code: {$mysqli->errno} ({$mysqli->error})</p>";


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


// -------------------------------------------------------------------------------------------
//
// Prepare the text
//
$html = "<h1>Create tables using foreign keys and multi_query and setting storage engine InnoDB</h2>";
$html .= "<p>Verifying MySQL problem: <a href='http://bugs.mysql.com/bug.php?id=40877'>http://bugs.mysql.com/bug.php?id=40877</a></p>";
$html .= "<p><a href='source.php?dir=&file=" . basename(__FILE__) . "'>Sourcecode</a></p>";
//$html .= "<p><a href='" . basename(__FILE__) . "?engine=no'>Execute testcase using NO storage engine defined.</a><br />";
$html .= "<p><a href='" . basename(__FILE__) . "?engine=myisam'>Execute testcase using ENGINE=MyISAM</a><br />";
$html .= "<a href='" . basename(__FILE__) . "?engine=innodb'>Execute testcase using ENGINE=InnoDB</a><br /></p>";

$html .= "<h2>Details on environment</h2>";
$html .= "<p>PHP is: " . phpversion() . "</p>";
$html .= "<p>MySQL client is: " . mysqli_get_client_version() . "</p>";
$html .= "<p>MySQL protocol is: " . $mysqli->protocol_version . "</p>";
$html .= "<p>MySQL server is: " . $mysqli->server_info . "</p>";

$html .= "<p>{$htmlMulti}</p>";
$html .= "<p>{$htmlSingle}</p>";

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$title = "Test CREATE TABLE using engine innodb and foreign keys";
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
