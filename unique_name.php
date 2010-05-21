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
echo "<a href='source.php?'>Source</a> | ";

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


?>