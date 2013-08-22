<?php

    $file = $_GET['file'];
    $path = $_GET['path'];

    $file_result = "../../../". $path . $file;

header('Access-Control-Allow-Origin: *');


if (file_exists($file_result) && is_readable($file_result)) {

    header("Content-Type: application/text");
    header("Content-Disposition: inline; filename=\"$file\"");
    header('Expires: ' . gmdate('D, d M Y H:i:s', gmmktime() - 3600) . ' GMT');
    header("Content-Length: " . filesize($file_result));
    // Special headers for IE 6
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    $fp = fopen($file_result, "rb");
    fpassthru($fp);
    fclose($fp);

} else {
    die("Arquivo \"$file_result\" n&atilde;o foi encontrado ou est&aacute; inacessivel.");
}


?>