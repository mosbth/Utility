<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: source.php
//
// Description: Shows a directory listning and view content of files.
//
// Author: Mikael Roos, mos@bth.se
//
// Change history:
// 
// 2010-09-07: 
// Added replacement of \t with spaces as configurable option ($SPACES).
// Removed .htaccess-files. Do not show them.
//
// 2010-04-27: 
// Hide password even in config.php~.
// Added rownumbers and enabled linking to specific row-number.
//

// -------------------------------------------------------------------------------------------
//
// Settings for this pagecontroller. Review and change these settings to match your own
// environment.
//
error_reporting(E_ALL);

// Separator between directories and files, change between Unix/Windows
$SEPARATOR = DIRECTORY_SEPARATOR; 	// Using built-in PHP-constant for separator.
//$SEPARATOR = '/'; 	// Unix, Linux, MacOS, Solaris
//$SEPARATOR = '\\'; 	// Windows 

// Show the content of files named config.php, except the rows containing DB_USER, DB_PASSWORD
$HIDE_DB_USER_PASSWORD = TRUE; // TRUE or FALSE

// Which directory to use as basedir for file listning, end with separator.
// Default is current directory
$BASEDIR = '.' . $SEPARATOR;

// Show syntax of the code, currently only supporting PHP or DEFAULT.
// PHP uses PHP built-in function highlight_string.
// DEFAULT performs <pre> and htmlspecialchars.
// HTML to be done.
// CSS to be done.
$SYNTAX = 'PHP'; 	// DEFAULT or PHP
$SPACES = '  '; 	// Number of spaces to replace each \t

// The link to this page. You may want to change it from relative link to absolute link.
$HREF = 'source.php?';


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$html = <<<EOD
<header>
<h1>Show sourcecode</h1>
<p>
The following files exists in this folder. Click to view.
</p>
</header>
EOD;


// -------------------------------------------------------------------------------------------
//
// Verify the input variable _GET, no tampering with it
//
$currentdir	= isset($_GET['dir']) ? $_GET['dir'] : '';

$fullpath1 	= realpath($BASEDIR);
$fullpath2 	= realpath($BASEDIR . $currentdir);
$len = strlen($fullpath1);
if(	strncmp($fullpath1, $fullpath2, $len) !== 0 ||
	strcmp($currentdir, substr($fullpath2, $len+1)) !== 0 ) {
	die('Tampering with directory?');
}
$fullpath = $fullpath2;
$currpath = substr($fullpath2, $len+1);


// -------------------------------------------------------------------------------------------
//
// Show the name of the current directory
//
$start		= basename($fullpath1);
$dirname 	= basename($fullpath);
$html .= <<<EOD
<p>
<a href='{$HREF}dir='>{$start}</a>{$SEPARATOR}{$currpath}
</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Open and read a directory, show its content
//
$dir 	= $fullpath;
$curdir1 = empty($currpath) ? "" : "{$currpath}{$SEPARATOR}";
$curdir2 = empty($currpath) ? "" : "{$currpath}";

$list = Array();
if(is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
        	if($file != '.' && $file != '..' && $file != '.svn' && $file != '.git' && $file != '.htaccess') {
        		$curfile = $fullpath . $SEPARATOR . $file;
        		if(is_dir($curfile)) {
          	  		$list[$file] = "<a href='{$HREF}dir={$curdir1}{$file}'>{$file}{$SEPARATOR}</a>";
          	  	} else if(is_file($curfile)) {
          	  	  $list[$file] = "<a href='{$HREF}dir={$curdir2}&amp;file={$file}'>{$file}</a>";
          	  	}
          	 }
        }
        closedir($dh);
    }
}

ksort($list);

$html .= '<p>';
foreach($list as $val => $key) {
	$html .= "{$key}<br />\n";
}
$html .= '</p>';


// -------------------------------------------------------------------------------------------
//
// Show the content of a file, if a file is set
//
$dir 	= $fullpath;
$file	= "";

if(isset($_GET['file'])) {
	$file = basename($_GET['file']);

	// Get the content of the file
	$content = file_get_contents($dir . $SEPARATOR . $file);
	$content = str_replace("\t", $SPACES, $content);

	// Remove password and user from config.php, if enabled
	if($HIDE_DB_USER_PASSWORD == TRUE && 
		 ($file == 'config.php' || $file == 'config.php~')) {

		$pattern[0] 	= '/(DB_PASSWORD|DB_USER)(.+)/';
		$replace[0] 	= '/* <em>\1,  is removed and hidden for security reasons </em> */ );';
		
		$content = preg_replace($pattern, $replace, $content);
	}
	
	// Show syntax if defined
	if($SYNTAX == 'PHP') {
		$content = highlight_string($content, TRUE);
		$sloc = 0;
		$i=0;
		$rownums = "";
		$text = "";
		$a = explode('<br />', $content);		
		foreach($a as $row) {
			$i++;
			$sloc += (empty($row)) ? 0 : 1;
			$rownums .= "<a id='L{$i}' href='#L{$i}'>{$i}</a><br />";
			$text .= $row . '<br />';
		}
		$content = <<< EOD
<div class='container'>
<div class='header'>
<!-- {$i} lines ({$sloc} sloc) -->
{$i} lines
</div>
<div class='rows'>
{$rownums}
</div>
<div class='code'>
{$text}
</div>
</div>
EOD;
	} 
	
	// DEFAULT formatting
	else {
		$content = htmlspecialchars($content);
		$content = "<pre>{$content}</pre>";
	}
	
	$html .= <<<EOD
<h3>{$file}</h3>
{$content}
EOD;
}


// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$title = "Show sourcecode";
$charset = "utf-8";
$language = "en";
 
$html = <<< EOD
<!DOCTYPE html>
<html lang="{$language}">
<head>
	<meta charset="{$charset}" />
	<title>{$title}</title>
 	<style>
 		div.container {
			min-width: 40em;
			font-family: monospace;
			font-size: 1em;
 		}
 		div.header {
			color: #000;
			font-size: 1.1em;
			border: solid 1px #999;
			border-bottom: 0px;
			background: #cacaca;
			padding: 0.5em 0.5em 0.5em 0.5em;
		}	
 		div.rows {
 			float: left;
 			text-align: right;
			color: #999;
			border: solid 1px #999;
			background: #cacaca;
			padding: 0.5em 0.5em 0.5em 0.5em;
		}	
		div.rows a:link,
		div.rows a:visited,
		div.rows a:hover,
		div.rows a:active  { 
			text-decoration:none; 
			color: inherit;
		}
 		div.code {
 			white-space: nowrap;
			border: solid 1px #999;
			background: #f9f9f9;
			padding: 0.5em 0.5em 0.5em 3.5em;
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