<?php

ob_start();

// I route basically everything through this, since the site is so simple.

$url = parse_url($_GET['url']);

switch ($url['path']) {
    case 'meta.json':
        include '../inc/meta.json';
        break;
    case 'edition/':
    case 'edition':
        if (!isset($_GET['delivery_count'])) {
            $edition = 1;
        } else {
            $edition = (int)$_GET['delivery_count'] + 1;
        }

        // Get available editions from DropBox.
        $editions = array();
        $dir = opendir('../dropbox');

        while ($filename = readdir($dir)) {
            if (ctype_digit(substr($filename, 0, 4))) {
                list($number, $name) = explode('-', $filename, 2);
                $editions[(int)$number] = $name;
            }
        }
        asort($editions);

        if (!isset($editions[$edition])) {
            $edition = count($editions);
        }

        $filename = sprintf('../dropbox/%4d-%s/index.html');

        $etag = md5(file_get_contents($filename));
        header("ETag: $etag");
        readfile($filename);
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

