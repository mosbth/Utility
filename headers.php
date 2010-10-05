<?php

$charset = isset($_GET['charset']) &&  !empty($_GET['charset']) ? strip_tags($_GET['charset']) : 'iso-8859-1';
$content = isset($_GET['content']) &&  !empty($_GET['content']) ? strip_tags($_GET['content']) : 'text/html';

$header = "";
$xml = "";
$doctype = "";
$html = "";
$meta = "";


if(isset($_GET['header'])) {
	$header = "header(\"Content-Type: {$content}; charset={$charset}\");";
	header("Content-Type: {$content}; charset={$charset}");
}

if(isset($_GET['xml'])) {
	$xml = "<?xml version='1.0' encoding='{$charset}' ?>";
	echo $xml, "\n";
}

if(isset($_GET['doctype'])) {
	$doctype = <<<EOD
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
EOD;
	echo $doctype, "\n";
}

if(isset($_GET['html'])) {
	$html = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">';
	echo $html, "\n";
}

if(isset($_GET['meta'])) {
	$meta = "<head>\n<meta http-equiv='Content-Type' content='{$content}; charset={$charset};'/>\n</head>";
	echo $meta, "\n";
}

// Create link to current page
$refToThisPage = "http";
$refToThisPage .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
$refToThisPage .= "://";
$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
	(($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? ''	: ":{$_SERVER['SERVER_PORT']}");
$refToThisPage .= $_SERVER["SERVER_NAME"] . $serverPort . $_SERVER["REQUEST_URI"];

?>
<body>
<p><a href="http://github.com/mosbth/Utility/blob/master/<?php echo basename(__FILE__); ?>"><em>This file is part of Utility project on GitHub</em></a></p>

<h1>Testcase for headers</h1>

<p>Current header was:</p>
<pre style='border: 1px solid grey;'>
<?php 
echo $header, "<br />"; 
echo htmlspecialchars($xml, ENT_NOQUOTES, "UTF-8"), "<br />"; 
echo htmlspecialchars($doctype, ENT_NOQUOTES, "UTF-8"), "<br />"; 
echo htmlspecialchars($html, ENT_NOQUOTES, "UTF-8"), "<br />"; 
echo htmlspecialchars($meta, ENT_NOQUOTES, "UTF-8"), "<br />"; 
?></pre>

<p>Change request-url to alter the header-content.</p>
<p>Current request was: ?<?php echo htmlspecialchars($_SERVER['QUERY_STRING'], ENT_NOQUOTES, "UTF-8"); ?></p>

<p><a href="http://web-sniffer.net/?url=<?php echo urlencode($refToThisPage); ?>">Check current link at web-sniffer.net</a></p>

<hr />

<p><a href="?xml&amp;doctype&amp;html&amp;meta&amp;header">Full header, all stuff included (<code>?header&amp;xml&amp;doctype&amp;html&amp;meta</code>)</a></p>

<p><a href="?xml&amp;doctype&amp;html&amp;meta&amp;header&amp;charset=utf-8">Same but with charset=utf-8 (<code>?header&amp;xml&amp;doctype&amp;html&amp;meta&amp;charset=utf-8</code>)</a></p>

<hr />
<p><a href="?xml&amp;doctype&amp;html&amp;meta&amp;header&amp;content=<?php echo urlencode("application/xhtml+xml"); ?>">This header can display a svg-image (application/xhtml+xml)</a></p>

<p><a href="?xml&amp;doctype&amp;html&amp;header&amp;content=<?php echo urlencode("application/xhtml+xml"); ?>">Same but with header(), without meta</a></p>

<p><a href="?xml&amp;doctype&amp;html&amp;meta&amp;content=<?php echo urlencode("application/xhtml+xml"); ?>">Same but without header(), with meta</a></p>


<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" version="1.0" width="140" height="140"><g transform="translate(10,10)"><g id="scale" transform="scale(20,20)"><g id="grid" 
style="fill:none;stroke-linejoin:round;stroke-linecap:butt;stroke:#000000;stroke-width:.1;"><path d="m 0 0 L 6 0 L 6 6 L 0 6 Z" /><path d="M 0 2 L 6 2" /><path d="M 0 4 L 6 4" /><path d="M 2 0 L 2 6" /><path d="M 4 0 L 4 6" /></g><g id="dots" style="fill:#000000"><ellipse cx="3" cy="1" rx=".7" ry=".7" id="C1" /><ellipse cx="5" cy="3" rx=".7" ry=".7" id="C2" /><ellipse cx="1" cy="5" rx=".7" ry=".7" id="C3" /><ellipse cx="3" cy="5" rx=".7" ry=".7" id="C4" /><ellipse cx="5" cy="5" rx=".7" ry=".7" id="C5" /></g></g></g></svg>

<hr />
<p>Testing &aring;&auml;&ouml;&Aring;&Auml;&Ouml;.</p> 
<p>Testing åäöÅÄÖ (saved as utf-8).</p> 
<p><a href="?xml&amp;doctype&amp;html&amp;meta&amp;header&amp;charset=utf-8">Charset utf-8</a></p>
<p><a href="?xml&amp;doctype&amp;html&amp;meta&amp;header&amp;charset=iso-8859-1">Charset iso-8859-1</a></p>

<hr />
<p><a href="source.php?file=<?php echo basename(__FILE__); ?>"><em>Sourcecode</em></a></p>
</body>
</html>