<?php 
/**
 * Show how to include source.php in a context.
 *
 * When you have a website with a header and footer you would most likely want to integrate the
 * output of source.php in that context. You can then set some parameters and include source.php,
 * as the result you will get the HTML than can be echoed out together with css-style information.
 * This file is an example on how to do this. Read the comments in source.php to get a grip on
 * the arguments used.
 *
 */

// Include code from source.php to display sourcecode-viewer
$sourceBasedir=__DIR__;  // Which directory to dsiplay
$sourceNoEcho=true;      // Do not echo result, store in $sourceBody instead
$sourceNoIntro=true;     // Do not display the intro, I want to write my own header and ingress
include("source.php");
$pageStyle=$sourceStyle; // $sourceStyle contains the CSS you need to present the HTML, put in HTML head.
$pageBody=$sourceBody;   // The actual content of source.php, echo it out.

?>

<!DOCTYPE html>
<head>
<meta charset=utf-8>
<title>Example on integrating source.php in another context</title>
<style><?php echo $pageStyle; ?></style>
</head>
<body>
	<p><em>Here I could include my own header.</em></p>
	
  <h1>My own display of sourcecode</h1>
  
  <p>Here it comes!</p>

	<?php echo $pageBody; ?>

	<hr>
	<p><em>Here I could include my own footer.</em></p>
  <p>
    <a href="http://validator.w3.org/check/referer">HTML5</a>  
    <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a>
    <a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">CSS3</a>
    <a href="http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance">Unicorn</a>
    <a href="source.php?file=<?php echo $_SERVER['PHP_SELF'] ?>#file">Sourcecode</a>
  </p>
</body>
</html>
