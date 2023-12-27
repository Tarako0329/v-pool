<?php
    require "php_header.php";
	if(empty($_SESSION["uid"])){
        $_SESSION["MSG"]="セッション切れです。再度ログインしてください。";
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: login.php");
        exit();
    }
   //var_dump(getFileList("./upload/ryota/"));
?>
<!DOCTYPE html>
<html lang='ja' >

<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head_bs5.php" 
    ?>
    <TITLE>Video Uploader</TITLE>
    <style>
    </style>
</head>
<BODY id = 'body' style='background:black;' >
    <HEADER class='text-center' style='color:#FFA400'>
        <h1><a href='/' class='a_none'>Video Uploader</a></h1>
        <div class='youkoso'><?php echo "ようこそ".$_SESSION["name"]."さん";?></div>
    </HEADER>
    <MAIN style='padding-bottom:0;padding-left:10px;padding-right:10px;'>
        <div style='width:100%;height:100%;'>
            <iframe src="V-manager-iframe.php?lv=0%" width="100%" height="100%"></iframe>
        </div>
    </MAIN>

</BODY>
</html>









