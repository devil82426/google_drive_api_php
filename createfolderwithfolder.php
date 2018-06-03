<?php
require './GDrive.php';
$GDrive = new GDrive();
//你要創建的資料夾名稱
$folder_name = "guava_second_folder";
//你要上傳到哪個資料夾的ID
$parent_folder_id = "1jvKtiqUkJPHkR3szrf40zabv6Xurbaj3";
$ID = $GDrive->CreateFolderWithFolder($folder_name, $parent_folder_id);
printf("%s\n", $ID);
?>