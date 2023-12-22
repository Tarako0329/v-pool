<?php
  require "php_header.php";

  try{
    $pdo_h->beginTransaction();
    $sql = "delete from loginkeeper where uid =:id and token =:token";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
    $stmt->bindValue("token", $_COOKIE["vpool"], PDO::PARAM_STR);
    $stmt->execute();
    $pdo_h->commit();
  }catch(Exception $e){
    $_SESSION["MSG"]="loginkeeper削除失敗。";
    log_writer("loginkeeper削除失敗。",$e);
    $pdo_h->rollBack();
  }


  setCookie("vpool", '', -1, "/", "", false, TRUE); // secure, httponly
  $_SESSION=[];
  $_SESSION["MSG"]="ログオフしました";
 
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: login.php");
  exit();
?>