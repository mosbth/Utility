<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" media="all" type="text/css" href="forms.css" title="Mos standard form layout">
	<link rel="shortcut icon" href="../../img/favicon.ico">
 	<title>Upload files</title>
</head>

<?php
// ---------------------------------------------------------------------------------------------
//
// SET ERROR REPORTING TO ON
// Always set error reporting to on, will save a load of time during development and testing
// 
error_reporting(-1);


// -------------------------------------------------------------------------------------------
//
// FUNCTION
// Function to open and read a directory, return its content as an array.
//
// $aPath: A path to the directory to scan for files. 
//
function readDirectory($aPath) {
	$list = Array();
	if(is_dir($aPath)) {
		if ($dh = opendir($aPath)) {
			while (($file = readdir($dh)) !== false) {
				if(is_file("$aPath/$file") && $file != '.htaccess') {
					$list[$file] = "$file";
				}
			}
			closedir($dh);
		}
	}
	sort($list, SORT_NUMERIC);
	return $list;
}


// ---------------------------------------------------------------------------------------------
//
// VALIDATE INCOMING VALUES FROM POST AND GET VARIABLES.
// Do some initial checking, validating and defining/setting of variables that will be used
// all through the script. 
//
//
$output = "";
$imageDir	= "img";

$FILE_UPLOAD_DISABLED=true;
if(is_readable('config.php')) {	require_once('config.php'); }

if($FILE_UPLOAD_DISABLED == true) {
	die("<strong><em>File upload is disabled.</em></strong>");
}

if(!isset($imageDir)) {
	die("<strong><em>sqlaid is enabled but missing directory to store files. Define \$imageDir and ensure its a writable directory.</em></strong>");
}

$dir = dirname(__FILE__) . "/$imageDir";
if(!(is_dir($dir) && is_writable($dir))) {
	die("<strong><em>The directory: '{$dir}' does not exists or is not writeable by the webserver.</em></strong>");
}


// ---------------------------------------------------------------------------------------------
//
// UPLOAD IMAGES
// Upload images to the server.
//
//	http://php.net/manual/en/features.file-upload.post-method.php
//	
if(!empty($_POST['doFileUpload'])) {
	$uploaddir = dirname(__FILE__) . "/$imageDir/";
	$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    $output .= "Filen laddas upp. ";
	} else {
    $output .= "Något gick fel när filen laddades upp. ";
	}
	
	// To debug
	echo "<pre>"; print_r($_FILES); echo "</pre>";
}


// ---------------------------------------------------------------------------------------------
//
// DELETE IMAGES
// Delete images from the server.
//	
if(!empty($_GET['doRemoveImage'])) {
	$dir 	= dirname(__FILE__) . "/$imageDir/";
	$file = basename(strip_tags($_GET['doRemoveImage']));
	if(is_file("$dir/$file")) {
		unlink("$dir/$file");
    $output .= "Bilden $dir/$file raderas. ";
	}
}


// ---------------------------------------------------------------------------------------------
//
// DISPLAY LIST OF IMAGES
// Display the images that were uploaded.
//
$dir = dirname(__FILE__) . "/$imageDir/";
$files = readDirectory($dir);
sort($files);

$images = "";
foreach($files as $val) {
	$parts = pathinfo("$dir/$val");
	if(is_file("$dir/$val") && isset($parts['extension']) && $parts['extension'] != 'php') {
		$del = "";
		if(is_writeable("$dir/$val")) {
			$del = " <a href='?id=$id&amp;doRemoveImage=$val' title='Radera bilden'>x</a>";
		}
		$images	.= "<a href='$imageDir/$val' title='Visa bilden $imageDir/$val'>$val</a>{$del}<br>";
	}
}

?>

<body>
	<p>Följande bilder finns sparade:
	<p><?php echo $images; ?></p>

	<form class="standard w600" method=post>
	 <fieldset>
		<legend>Ladda upp egna bilder</legend>
	
		<form enctype="multipart/form-data" method="POST">
			<!-- MAX_FILE_SIZE must precede the file input field -->
			<input type="hidden" name="MAX_FILE_SIZE" value="9000000" />
			<!-- Name of input element determines name in $_FILES array -->
			<input name="userfile" type="file" />
			<input type="submit" name="doFileUpload" value="Ladda upp" />
		</form>
		
		<output><?php echo $output; ?></output>
		
	 </fieldset>
	</form>
	<p><a href="source.php?file=<?php echo basename(__FILE__); ?>"><em>Källkod</em></a>
</body>
</html>
