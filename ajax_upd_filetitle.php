<?php
require "php_header.php";
log_writer('\$_GET',$_GET);
$return_satas="success";

try{
  $pdo_h->beginTransaction();
  $sql = "update filelist set title = :title where uid = :id and fileNo = :fileNo";
  $stmt = $pdo_h->prepare($sql);
  $stmt->bindValue("title", $_GET["title"], PDO::PARAM_STR);
  $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("fileNo", $_GET["fileNo"], PDO::PARAM_INT);
  $stmt->execute();
  $pdo_h->commit();
}catch(Exception $e){
  $pdo_h->rollBack();
  $return_satas = "error:".$e;
}



//jsonとして出力
header("HTTP/1.1 200 Ok");
echo $return_satas;
?>