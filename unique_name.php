<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: unique_name.php
//
// Description: Encode a filename/irl/other to a unique string which can safely be stored on disk
// or in the database as a unique id.
//
// Can be used on file upload to create a filename or as a way to create short urls.
//
// Author: Mikael Roos, mos@bth.se
//
// Revision history:
//
// 2010-05-21: Initial effort to put it together.
//

// -------------------------------------------------------------------------------------------
//
// Present a way to view the file.
// 
echo "<p>";
echo "<a href='source.php??dir=&file=unique_name.php'>Source</a> | ";
echo "<a href='http://github.com/mosbth/Utility/blob/master/unique_name.php'>On GitHub</a> | ";
echo "</p>";

// -------------------------------------------------------------------------------------------
//
// Take a filename and convert it to avoid poblems with charsets.
// 
$digest = 'The filename.txt';

// Take 1
$hash = strtr(base64_encode(md5($digest, true)), '+/=', '-_(');
echo $hash . "<br />";

// Take 2
$hash = strtr(base64_encode(uniqid($digest)), '+/=', '-_(');
echo $hash . "<br />";

// Take 3
$hash = strtr(base64_encode(uniqid()), '+/=', '-_(');
echo $hash . "<br />";

// Take 4
$hash = uniqid();
echo $hash . "<br />";


//
// http://www.snippetit.com/2009/04/php-short-url-algorithm-implementation/
//
function shorturl($input) {
  $base32 = array (
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
    'i', 'j', 'k', '9', 'm', 'n', '6', 'p',
    'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
    'y', 'z', '7', '8', '2', '3', '4', '5'
    );

  $hex = md5($input);
  $hexLen = strlen($hex);
  $subHexLen = $hexLen / 8;
  $output = array();

  for ($i = 0; $i < $subHexLen; $i++) {
    $subHex = substr ($hex, $i * 8, 8);
    $int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
    $out = '';

    for ($j = 0; $j < 6; $j++) {
      $val = 0x0000001F & $int;
      $out .= $base32[$val];
      $int = $int >> 5;
    }

    $output[] = $out;
  }

  return $output;
}

$input = 'http://dev.phpersia.org/persia';
$output = shorturl($input);

echo "<pre>";
echo "Input  : $input\n";
echo "Output : {$output[0]}\n";
echo "         {$output[1]}\n";
echo "         {$output[2]}\n";
echo "         {$output[3]}\n";
echo "\n";

$input = 'http://dev.phpersia.org/utility';
$output = shorturl($input);

echo "Input  : $input\n";
echo "Output : {$output[0]}\n";
echo "         {$output[1]}\n";
echo "         {$output[2]}\n";
echo "         {$output[3]}\n";
echo "\n";
echo "</pre>";


?>