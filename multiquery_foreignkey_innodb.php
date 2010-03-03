<?php
// ===========================================================================================
//
// multiquery_foreignkey_innodb.php
//
// Common file for test purpose. Easy to use to test database connections, depends on 
// config.php where all database details are. 
//
// This particular testcase is to verify if there is a problem with creating tables
// using the InnoDB engine in a multi_query with foreign key constraints.
//
// Author Mikael Roos, mos@bth.se
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
// Prepare and perform a SQL query.
//
$tableProfessor = 'test_professor';
$tableGrade     = 'test_grade';

$query = <<<EOD
DROP TABLE IF EXISTS {$tableGrade};
DROP TABLE IF EXISTS {$tableProfessor};

CREATE TABLE {$tableProfessor} (
    idProfessor INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    nameProfessor CHAR(40),
    infoProfessor CHAR(100),
    pictureProfessor CHAR(100)
) ENGINE=InnoDB;

CREATE TABLE {$tableGrade} (
    idGrade INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    Grade_idProfessor INT,
  FOREIGN KEY (Grade_idProfessor) REFERENCES {$tableProfessor} (idProfessor),
    valueGrade INT,
    commentGrade CHAR(100),
    dateGrade DATETIME
) ENGINE=InnoDB;

EOD;


$res = $mysqli->multi_query($query)
                    or die("Could not query database");

// -------------------------------------------------------------------------------------------
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


// -------------------------------------------------------------------------------------------
//
// Prepare the text
//
$html = "<h1>Create tables using foreign keys, innodb and multi_query</h2>";

$html .= "<h2>Details on environment</h2>";
$html .= "<p>PHP is: " . phpversion() . "</p>";
$html .= "<p>MySQL client is: " . mysqli_get_client_version() . "</p>";
$html .= "<p>MySQL server is: " . $mysqli->server_info . "</p>";

$html .= "<h2>Testcase</h2>";
$html .= "<p>Query=<br/><pre>{$query}</pre></p>";
$html .= "<p>Antal lyckade statements: {$statements}</p>";
$html .= "<p>Error code: {$mysqli->errno} ({$mysqli->error})</p>";
$html .= "<a href='source.php?dir=&file=" . basename(__FILE__) . "'>Sourcecode</a>";

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
