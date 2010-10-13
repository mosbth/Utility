<?php
$charset = "utf-8";
$mime = (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) ? "application/xhtml+xml" : "text/html";
header("content-type:$mime;charset=$charset");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title></title>
  </head>
  <body>
   <p><a href="http://validator.nu/?doc=<?php echo urlencode("http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']); ?>">This page validates as XHTML5 on validator.nu</a>.</p>
   <p><a href="source.php?file=xhtml5.php">Sourcecode</a></p>
  </body>
</html>
