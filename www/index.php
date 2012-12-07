<?php

ob_start();

// I route basically everything through this, since the site is so simple.

$url = parse_url($_GET['url']);

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

switch ($url['path']) {
    case 'edition/':
    case 'edition':
        if (!isset($_GET['delivery_count'])) {
            $edition = 1;
        } else {
            $edition = (int)$_GET['delivery_count'] + 1;
        }

        if (!isset($editions[$edition])) {
            $edition = count($editions);
        }

        $filename = sprintf('../dropbox/%04d-%s/index.html', $edition, $editions[$edition]);

        $etag = md5(file_get_contents($filename));
        header("ETag: $etag");
        readfile($filename);
        break;
    case 'sample/':
    case 'sample':
        // Always give latest.
        $edition = count($editions);
        $filename = sprintf('../dropbox/%04d-%s/index.html', $edition, $editions[$edition]);
        readfile($filename);
        break;
    default:
        if (substr($_GET['url'], -4) == '.png') {
            $image = basename($_GET['url']);
            $name = substr($image, 0, strlen($_GET['url']) - 4);

            if ($edition = array_search($name, $editions)) {
                header('Content-Type: image/png');
                $filename = sprintf('../dropbox/%04d-%s/%s.png', $edition, $editions[$edition], $editions[$edition]);
                readfile($filename);
            }
        } elseif ($url['path'] != '') {
            // Primitive 404 handling.
            header('Location: http://getglyph.org/');
        } else {
            // Temporary redirect until we have a home page.
            header('Location: http://getglyph.org/sample');
            echo 'getglyph.org';
        }
        break;
}


?>

