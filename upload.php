<?php
require './GDrive.php';
$GDrive = new GDrive();
//你上傳檔案後要叫的名稱
$filename = "guava_upload_file.png";
//你要上傳的檔案路徑
$fulldir = "./photo.png";
$ID = $GDrive->UploadData($filename, $fulldir);
printf("%s\n", $ID);
?>