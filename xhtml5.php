<?php
$charset = "utf-8";
$mime = (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) ? "application/xhtml+xml" : "text/html";
header("content-type:$mime;charset=$charset");

// Create link to current page
$refToThisPage = "http";
$refToThisPage .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
$refToThisPage .= "://";
$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
(($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
$refToThisPage .= $_SERVER["SERVER_NAME"] . $serverPort . $_SERVER["REQUEST_URI"];

?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title></title>
  </head>
  <body>
   <p><a href="http://validator.nu/?doc=<?php echo $refToThisPage; >">This page validates as XHTML5 on validator.nu</a>.</p>
   <p><a href="source.php?file=xhtml5.php">Sourcecode</a></p>
  </body>
</html>
