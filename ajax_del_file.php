<?php
require "php_header.php";
$return_satas="success";

$rtn = csrf_checker(["V-manager-iframe.php"],["G","C"]);
if($rtn !== true){
  $return_satas = "error:".$rtn;
  /*
  header("HTTP/1.1 200 Ok");
  echo $return_satas;
  exit();
  */
}else if(!empty($_GET)){
  $token = csrf_create();
  log_writer('\$_GET',$_GET);

  $filename = $_GET["F"];
  $fileNO = $_GET["FN"];
  try{
    $pdo_h->beginTransaction();
    $sql = "delete from filelist where uid = :id and fileNo = :fileNo and filename = :name";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
    $stmt->bindValue("fileNo", $fileNO, PDO::PARAM_INT);
    $stmt->bindValue("name", $filename, PDO::PARAM_INT);
    $stmt->execute();

    $file=SAVEDIR.$_SESSION["uid"]."/".$filename;

    if (unlink($file)){
      //echo $file.'の削除に成功しました。';
      $pdo_h->commit();
    }else{
      $return_satas = 'ファイルの削除に失敗しました。';
      $pdo_h->rollBack();
    }
    
  }catch(Exception $e){
    $pdo_h->rollBack();
    $return_satas = "error:".$e;
  }
  
}else{
  $return_satas = "error:パラメータ不正";
}

$msg = array(
  "status" => $return_satas
  ,"token" => $token
);


header('Content-type: application/json');
echo json_encode($msg, JSON_UNESCAPED_UNICODE);
exit();
?>