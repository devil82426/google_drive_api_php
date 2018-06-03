<?php
require './GDrive.php';
$GDrive = new GDrive();
//你要創建的資料夾名稱
$folder_name = "guava_folder";
$ID = $GDrive->CreateFolder($folder_name);
printf("%s\n", $ID);
?>