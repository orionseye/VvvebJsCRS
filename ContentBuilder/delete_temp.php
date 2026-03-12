<?php
$serviceID = (int)$_POST['serviceID'];
$tempFile = '_temp/temp_' . $serviceID . '.html';
if(file_exists($tempFile)) unlink($tempFile);
?>