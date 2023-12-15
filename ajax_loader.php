<?php
require "php_header.php";

$results = getFileList("./upload/".$_GET["name"]."/");
$filelists = array();
$counter = 0;
$dis_counter=0;

$iCount = (!empty($_GET["iCount"])?$_GET["iCount"]:10);
//echo $iCount."<br>";
if($_GET["first"]==="true"){
    $_SESSION["readed_count"] = 0;
}else{
    $_SESSION["readed_count"]=(empty($_SESSION["readed_count"])?0:$_SESSION["readed_count"]);
}
foreach($results as $result){
    if($counter < $_SESSION["readed_count"]){
        //前回の出力件数までカウントアップ
        $counter++;
        continue;
    }
    
    $filelists[] = array(
        'index' => $counter
        ,'type' => (mime_content_type($result)=="directory"?"directory":"file")
        ,'src' => $result."#t=0.01"
        ,'name' => basename($result)
    );
    $counter++;
    $dis_counter++;
    
    if($dis_counter >= $iCount){
        break;  //表示件数がパラメータ値を超えたらブレイク
    }
}
$_SESSION["readed_count"]= $counter;

//jsonとして出力
header('Content-type: application/json');
echo json_encode($filelists, JSON_UNESCAPED_UNICODE);
?>