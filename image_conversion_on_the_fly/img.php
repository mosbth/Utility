<?php
/**
 * Resize images on the fly using cache.
 * 
 * Credits: Bit Repository
 * Source URL: http://www.bitrepository.com/resize-an-image-keeping-its-aspect-ratio-using-php-and-gd.html
 * Mos added caching and rewrote some code.
 *
 */
$source_img_dir = __DIR__.'/img';
$cache_dir = $source_img_dir.'/cache';
$extensions = array('jpg', 'jpeg', 'png', 'bmp', 'gif');
$valid = implode(', ', $extensions);
$usage = <<<EOD
<p>You must set the src attribute pointing to a valid image.
For example: <a href="img.php?src=higher.jpg">img.php?src=higher.jpg</a> or <a href="img.php?src=wider.jpg">img.php?src=wider.jpg</a>.
</p>
<p>The image name must have a valid extension: ({$valid})</p>
<p>Try using any combination of the following arguments to the query string: 'width=40', 'height=40', 'no-ratio'.</p> 
EOD;

// Set some limit on script if processing is carried away
set_time_limit(20);

// Get the filename and do some checks on it
isset($_GET['src']) or die($usage);
$filename = basename($_GET['src']);
$fileparts = pathinfo($filename);
in_array($fileparts['extension'], $extensions) or die('Not a valid extension.');

// Get all arguments and set for coming calculation
$new_width        = isset($_GET['width']) ? $_GET['width'] : null;
$new_height       = isset($_GET['height']) ? $_GET['height'] : null;
$image_to_resize  = "$source_img_dir/$filename";
$ratio            = isset($_GET['no-ratio']) ? false : true; // Keep Aspect Ratio?
$new_image_name   = $filename;


// Do some sanity checks
is_null($new_width)  || is_numeric($new_width)  or die('Width not numeric');
is_null($new_height) || is_numeric($new_height) or die('Height not numeric');
!is_file($image_to_resize) or die('Image file does not exists');


// Create path to the cached img, set its name based on passed arguments
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
  $save_folder = "$cache_dir/$cache";
  if(!is_dir($save_folder)) {
    mkdir($save_folder) or die('Failed to create cache directory.');
  }
}


// Check cache or process file
$image_to_output = $image_to_resize;
$time = filemtime($image_to_resize);
$cachefile = "$save_folder/$filename";
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
  include __DIR__.'/CResizeImage.php';
  $image = new CResizeImage();
  $image->new_width       = $new_width;
  $image->new_height      = $new_height;
  $image->image_to_resize = $image_to_resize;
  $image->ratio           = $ratio;
  $image->new_image_name  = $new_image_name;
  $image->save_folder     = $save_folder;
  $process = $image->Resize();
  $image_to_output = $process['new_file_path'];
}
die('hej');


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
