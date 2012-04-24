<?php
/**
 * Resize images on the fly using cache.
 * 
 * Credits: Bit Repository
 * Source URL: http://www.bitrepository.com/resize-an-image-keeping-its-aspect-ratio-using-php-and-gd.html
 * Mos added caching and rewrote some code.
 *
 */
set_time_limit(20);

// Source must be set or exit
if(!isset($_GET['src'])) {
  die('Must set src.');
}
$filename = $_GET['src'];

$new_width        = isset($_GET['width']) ? $_GET['width'] : null;
$new_height       = isset($_GET['height']) ? $_GET['height'] : null;
$image_to_resize  = __DIR__."/img/$filename";
$ratio            = isset($_GET['no-ratio']) ? false : true; // Keep Aspect Ratio?
$new_image_name   = $filename;


//Check that only figures is input
if($new_width && !is_numeric($new_width)) {
  die('Width not numeric');
}
if($new_height && !is_numeric($new_height)) {
  die('Height not numeric');
}


// Check that original file exists
is_file($image_to_resize) or die('File does not exists');


// Create path to the cached img
$cache = null;
$r = ($ratio ? null : 'r');
if($new_width && $new_height) {
  $cache = "h{$new_height}-w{$new_width}$r";
} else if($new_width) {
  $cache = "w{$new_width}$r";
} else if($new_height) {
  $cache = "h{$new_height}$r";
}
// Path where the new image should be saved. If it's not set the script will output the image without saving it 
$save_folder = null;
if($cache) {
  $save_folder = __DIR__."/img/cache/$cache";
  if(!is_dir($save_folder)) {
    mkdir($save_folder) or die('Failed to create cache directory.');
  }
}


// Check cache or process file
$image_to_output = $image_to_resize;
//$time = mktime(0,0,0,21,5,1980);
$time = filemtime($image_to_resize);
$cachefile = "{$save_folder}/{$filename}";
$recache = false;
if($save_folder && is_file($cachefile)) {
  $cachetime = filemtime($cachefile);
  if($cachetime < $time) {  
    $time = $cachetime;  
    $recache = true;  
  } else {  
    $image_to_output = $cachefile;
    $recache = false;  
  }
} else if($save_folder) {
  $recache = true;
}


// Resize
if($recache) {
  include __DIR__.'/incl/resize.image.class.php';
  $image = new CResizeImage();
  $image->new_width       = $new_width;
  $image->new_height      = $new_height;
  $image->image_to_resize = $image_to_resize;
  $image->ratio           = $ratio;
  $image->new_image_name  = $new_image_name;
  $image->save_folder     = $save_folder;
  $process = $image->resize();
  $image_to_output = $process['new_file_path'];
}


// Output
if(!$recache && isset($_SERVER['If-Modified-Since']) && strtotime($_SERVER['If-Modified-Since']) >= $time){  
  header("HTTP/1.0 304 Not Modified");
} else {  
  $info = GetImageSize($image_to_resize);
  $width = $info[0];
  $height = $info[1];
  $mime = $info['mime'];
  header("Content-Type: ".$mime);
  header('Last-Modified: ' . gmdate("D, d M Y H:i:s",$time) . " GMT");
  readfile($image_to_output);  
}  
