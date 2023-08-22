<?php


//require_once __DIR__ . '/vendor/autoload.php';
require_once  './vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/tmp'
]);
$mpdf->WriteHTML('<h1>Hello world!</h1>');
$mpdf->Output();

?>
