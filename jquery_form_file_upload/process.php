<?
    $type = $_POST['mimetype'];
    $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

    if ($type == 'xml') {
        header('Content-type: text/xml');
?>
<address attr1="value1" attr2="value2">
    <street attr="value">A &amp; B</street>
    <city>Palmyra</city>
</address>
<?
    }
    else if ($type == 'json') {
        // wrap json in a textarea if the request did not come from xhr
        if (!$xhr) echo '<textarea>';
?>

{
    "library": "jQuery",
    "plugin":  "form",
    "hello":   "goodbye",
    "tomato":  "tomoto",
    "xhr":  "<?php echo $xhr; ?>"
}

<?
        if (!$xhr) echo '</textarea>';
    }
    else if ($type == 'script') {
        // wrap script in a textarea if the request did not come from xhr
        if (!$xhr) echo '<textarea>';
?>

for (var i=0; i < 2; i++)
    alert('Script evaluated!');

<?
        if (!$xhr) echo '</textarea>';
    }
    else {
        // return text var_dump for the html request
        echo "VAR DUMP:<p />'{$xhr}'";
        var_dump($_POST);
        var_dump($_SERVER);
        foreach($_FILES as $file) {
            $n = $file['name'];
            $s = $file['size'];
            if (!$n) continue;
            echo "File: $n ($s bytes)";
        }
    }
?> 