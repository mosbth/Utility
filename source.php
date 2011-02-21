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
// 2011-02-21: 
// Can now have same link to subdirs, independently on host os. Links that contain / or \ is
// converted to DIRECTORY_SEPARATOR.
//
// 2011-02-04: 
// Can now link to #file to start from filename.
//
// 2011-01-26: 
// Added $sourceBasedir which makes it possible to set which basedir to use. This makes it
// possible to store source.php in another place. It does not need to be in the same directory 
// it displays. Use it like this (before including source.php):
// $sourceBasedir=dirname(__FILE__);
//
// 2011-01-20: 
// Can be included and integrated in an existing website where you already have a header 
// and footer. Do like this in another file:
// $sourceNoEcho=true;
// include("source.php");
// echo "<html><head><style type='text/css'>$sourceStyle</style></header>";
// echo "<body>$sourceBody</body></html>";
//
// 2010-09-14: 
// Thanks to Rocky. Corrected NOTICE when files had no extension.
//
// 2010-09-09: 
// Changed error_reporting to from E_ALL to -1.
// Display images of certain types, configurable option $IMAGES.
// Enabled display option of SVG-graphics.
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
error_reporting(-1);

// The link to this page. You may want to change it from relative link to absolute link.
$HREF = '?';

// Should the result be printed or stored in variables?
// Default is to print out the result, with header and everything.
// If $sourceNoEcho is set, no printing of the result will be done. It will only be stored 
// in the variables $sourceBody and $sourceStyle
//
if(!isset($sourceNoEcho)) {
	$sourceNoEcho = null;
}
$sourceBody="";  // resulting html
$sourceStyle=""; // css-style needed to print out the page

// Show the content of files named config.php, except the rows containing DB_USER, DB_PASSWORD
$HIDE_DB_USER_PASSWORD = TRUE; // TRUE or FALSE

// Separator between directories and files, change between Unix/Windows
$SEPARATOR = DIRECTORY_SEPARATOR; 	// Using built-in PHP-constant for separator.
//$SEPARATOR = '/'; 	// Unix, Linux, MacOS, Solaris
//$SEPARATOR = '\\'; 	// Windows 

// Which directory to use as basedir for file listning, end with separator.
// Default is current directory
$BASEDIR = '.' . $SEPARATOR;
if(isset($sourceBasedir)) {
	$BASEDIR = $sourceBasedir . $SEPARATOR;
}

// Display pictures instead of their source, if they have a certain extension (filetype).
$IMAGES = Array('png', 'gif', 'jpg', 'ico');

// Show syntax of the code, currently only supporting PHP or DEFAULT.
// PHP uses PHP built-in function highlight_string.
// DEFAULT performs <pre> and htmlspecialchars.
// HTML to be done.
// CSS to be done.
$SYNTAX = 'PHP'; 	// DEFAULT or PHP
$SPACES = '  '; 	// Number of spaces to replace each \t


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
$currentdir	= isset($_GET['dir']) ? preg_replace('/[\/\\\]/', $SEPARATOR, $_GET['dir']) : '';

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

	// Remove password and user from config.php, if enabled
	if($HIDE_DB_USER_PASSWORD == TRUE && 
		 ($file == 'config.php' || $file == 'config.php~')) {

		$pattern[0] 	= '/(DB_PASSWORD|DB_USER)(.+)/';
		$replace[0] 	= '/* <em>\1,  is removed and hidden for security reasons </em> */ );';
		
		$content = preg_replace($pattern, $replace, $content);
	}
	
	//
	// Display image if a valid image file
	//
	$pathParts = pathinfo($dir . $SEPARATOR . $file);
	$extension = isset($pathParts['extension']) ? strtolower($pathParts['extension']) : '';

	//
	// Display svg-image or enable link to display svg-image.
	//
	$linkToDisplaySvg = "";
	if($extension == 'svg') {
		if(isset($_GET['displaysvg'])) {
			header("Content-type: image/svg+xml");
			echo $content;
			exit;		
		} else {
			$linkToDisplaySvg = "<a href='{$_SERVER['REQUEST_URI']}&displaysvg'>Display as SVG</a>";
		}
	}
	
	//
	// Display image if a valid image file
	//
	if(in_array($extension, $IMAGES)) {
		$content = "<img src='{$currentdir}/{$file}' alt='[image not found]'>";

	//
	// Show syntax if defined
	//
	} elseif($SYNTAX == 'PHP') {
		$content = str_replace("\t", $SPACES, $content);
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
{$i} lines {$linkToDisplaySvg}
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
	
	//
	// DEFAULT formatting
	//
	else {
		$content = htmlspecialchars($content);
		$content = "<pre>{$content}</pre>";
	}
	
	$html .= <<<EOD
<h3 id="file"><a href="#file">{$file}</a></h3>
{$content}
EOD;
}



// -------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
$pageTitle = "Show sourcecode";
$pageCharset = "utf-8";
$pageLanguage = "en";
$sourceBody=$html;
$sourceStyle=<<<EOD
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
			background: #eee;
			padding: 0.5em 0.5em 0.5em 0.5em;
		}	
 		div.rows {
 			float: left;
 			text-align: right;
			color: #999;
			border: solid 1px #999;
			background: #eee;
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
			padding: 0.5em 0.5em 0.5em 0.5em;
			overflow:auto;
		}
EOD;


if(!isset($sourceNoEcho)) {
	// Print the header and page
	header("Content-Type: text/html; charset={$pageCharset}");
	echo <<<EOD
<!DOCTYPE html>
<html lang="{$pageLanguage}">
<head>
	<meta charset="{$pageCharset}" />
	<title>{$pageTitle}</title>
 	<style>{$sourceStyle}</style>
	<!--[if IE]> 
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>		
	<![endif]-->
</head>
<body>
	{$sourceBody}
</body>
</html>	
EOD;

	exit;
}
