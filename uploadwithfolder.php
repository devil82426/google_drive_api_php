<?php
require './GDrive.php';
$GDrive = new GDrive();
//你上傳檔案後要叫的名稱
$filename = "guava_upload_file.png";
//你要上傳的檔案路徑
$fulldir = "./photo.png";
//你要上傳到的資料夾的ID
$folderId = "1k1WkSBWzzxAWw5CSTK0DEk9Dsennp-8-";
$ID = $GDrive->UploadDataWithFolder($filename, $fulldir, $folderId);
printf("%s\n", $ID);
?>