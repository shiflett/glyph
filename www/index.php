<?php

$url = parse_url($_GET['url']);

switch ($url['path']) {
    case 'meta.json':
        include '../inc/meta.json';
        break;
    case 'edition/':
    case 'edition':
        if (!isset($_GET['delivery_count'])) {
            // Delivery count not set. Should never happen.
            $edition = 1;
        } else {
            $edition = (int)$_GET['delivery_count'] + 1;
        }

        if (!file_exists("../inc/{$edition}.inc")) {
            // Edition not found. TODO: Send latest.
            echo "<p>Edition {$edition} not found.</p>";
            exit;
        }

	$etag = md5(file_get_contents("../inc/{$edition}.inc"));
        header("ETag: $etag");
        readfile("../inc/{$edition}.inc");
        break;
    case 'sample/':
    case 'sample':
        readfile('../inc/1.inc');
        break;
    default:
        if (substr($_GET['url'], -4) == '.png') {
           $image = basename($_GET['url']);
           if (file_exists("../inc/images/{$image}")) {
               header('Content-Type: image/png');
               readfile("../inc/images/{$image}");
           }
        } elseif ($url['path'] != '') {
            header('Location: http://getglyph.org/');
        } else {
            // Temporary redirect until we have a home page.
            header('Location: http://getglyph.org/sample');
            echo 'getglyph.org';
        }
        break;
}


?>

