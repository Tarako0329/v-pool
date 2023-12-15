<?php
require "php_header.php";

// ファイルがあれば処理実行

//var_dump($_FILES);
$i=0;
file_put_contents("error_log",date('Y/m/d-H:i:s')."：uploading start...\n",FILE_APPEND);
if(isset($_FILES["upload_file"])){

    // アップロードされたファイル件を処理
    for($i = 0; $i < count($_FILES["upload_file"]["name"]); $i++ ){
        file_put_contents("error_log",date('Y/m/d-H:i:s')."：".$_FILES["upload_file"]["tmp_name"][$i]."\n",FILE_APPEND);
        // アップロードされたファイルか検査
        if(is_uploaded_file($_FILES["upload_file"]["tmp_name"][$i])){
            file_put_contents("error_log",date('Y/m/d-H:i:s')."：uploading...\n",FILE_APPEND);
            // ファイルをお好みの場所に移動
            move_uploaded_file($_FILES["upload_file"]["tmp_name"][$i], "./upload/eriko/" . $_FILES["upload_file"]["name"][$i]);
        }
    }

}else{
    //echo "no files";
    file_put_contents("error_log",date('Y/m/d-H:i:s')."：no files...\n",FILE_APPEND);
}

//$msg=$i." ファイルのアップロード完了";

//header("HTTP/1.1 301 Moved Permanently");
//header("Location: index.php?flg=".$msg);
//exit();
header('Content-type: application/json');
//echo "{stats:success}";
echo json_encode($_FILES, JSON_UNESCAPED_UNICODE);
?>