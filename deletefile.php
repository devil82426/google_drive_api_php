<?php
require './GDrive.php';
$GDrive = new GDrive();
//你要刪除的檔案ID
$fileId = "1a4cP0ce-cpiFLCMKypuJPrXYEg089YMK";
$GDrive->Deletefile($fileId);
?>