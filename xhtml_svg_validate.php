<?php 
header("Content-Type: application/xhtml+xml; charset=utf-8");
?>
<?xml version="1.0" encoding="utf-8" ?> 
<!DOCTYPE html PUBLIC
    "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN"
    "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:svg="http://www.w3.org/2000/svg" xml:lang="en">
<head>
	<title>Example that validates xhtml and svg</title>
</head>
<body>
	<p>This is a dice using inline svg. It validates.
	The trick is to use the correct DOCTYPE and to prepend each svg-tag with 'svg:'. View the page-source for details.</p>
	<p>
	<a href='http://validator.w3.org/check/referer'>XHTML</a> |
  <a href="source.php?file=xhtml_svg_validate.php">Sourcecode</a>
	</p>

	<svg:svg 
		 xmlns:z="http://www.w3.org/2000/svg"
		 xmlns:xlink="http://www.w3.org/1999/xlink"
		 xmlns:ev="http://www.w3.org/2001/xml-events"
		 width="100"
		 height="100">
		<svg:g transform="scale(0.15)">
			<svg:path style="fill:none;stroke:#000000;stroke-width:7;" d="M71.4594727,553.5C34.0805664,553.5,3.5,522.9189453,3.5,485.5400391
				V71.4594727C3.5,34.0820312,34.0805664,3.5,71.4594727,3.5h414.0805664C522.9179688,3.5,553.5,34.0820312,553.5,71.4594727
				v414.0805664C553.5,522.9189453,522.9179688,553.5,485.5400391,553.5H71.4594727z"/>
			<svg:circle style="fill:#FF724C;stroke:#740000;stroke-width:5;" cx="278.5" cy="278.5" r="57.1152344"/> 
		</svg:g>
	</svg:svg>

</body>
</html>