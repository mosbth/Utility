<?php
//
// Start a timer to time the generation of this request (start of the PHP-code)
//
$timerStart = microtime(true); 



// Do some PHP coding
sleep(1);

//
// Print out the timer (at the end of all PHP-code)
//
$time = 'Page generated in ' . round(microtime(true) - $timerStart, 5) . ' seconds.';

?>
<!DOCTYPE html>
<html lang=en>
  <head>
		<meta charset="utf-8">  
    <title>Example to time the php execution time when generating a webpage</title>
  </head>
  <body>
  	<h1>Timing the time it takes to execute the php-code in a webpage</h1>
   <p><?php echo $time; ?></p>
   <p><a href="source.php?file=timer.php">Sourcecode</a> | 
   <a href="http://github.com/mosbth/Utility">Part of Utility-project at GitHub</a></p>
  </body>
</html>

