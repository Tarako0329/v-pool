<?php
require "php_header.php";
//session_regenerate_id();
$success=false;

if(!empty($_POST)){
  $pass = passEx($_POST["pass"],$_POST["id"],NOM);
  if($_POST["login"]==="login"){
    $sql = "select * from user where uid = :id and pass = :pass";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("id", $_POST["id"], PDO::PARAM_STR);
    $stmt->bindValue("pass",$pass , PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetchAll();
    if(!empty($user[0]["uid"])){
      $_SESSION["uid"] = $user[0]["uid"];
      $_SESSION["name"] = $user[0]["name"];

      $success=true;

    }else{
      $_SESSION["MSG"]="ログインIDまたはパスワードが違います";
    }
  }else if($_POST["login"]==="newlogin"){
    try{
      $pdo_h->beginTransaction();
      $sql = "insert into user(uid,pass,name) values(:id,:pass,:name)";
      $stmt = $pdo_h->prepare($sql);
      $stmt->bindValue("id", $_POST["id"], PDO::PARAM_STR);
      $stmt->bindValue("pass", $pass, PDO::PARAM_STR);
      $stmt->bindValue("name", $_POST["nickname"], PDO::PARAM_STR);
      $stmt->execute();
      $pdo_h->commit();

      $_SESSION["uid"] = $_POST["id"];
      $_SESSION["name"] = $_POST["nickname"];
      mkdir("./upload/".$_SESSION["uid"], 0777);
      mkdir("./upload/".$_SESSION["uid"]."/chunks_temp_folder", 0777);

      $success=true;

    }catch(Exception $e){
      $_SESSION["MSG"]="登録に失敗しました。ユーザーIDがすでに登録されてます。";
      $pdo_h->rollBack();
    }
  }else{

  }
}
if($success){
  //リダイレクト
  $_SESSION["vpool"]=get_token();
  //setCookie("vpool", $token, time()+60*60*24*7, "/", "", TRUE, TRUE);//1week
  $sql = "insert into loginkeeper values(:id,:token,:kdatetime)";
  try{
    $pdo_h->beginTransaction();
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
    $stmt->bindValue("token", $_SESSION["vpool"], PDO::PARAM_STR);
    $stmt->bindValue("kdatetime", date("Y-m-d",strtotime("+7 day")), PDO::PARAM_STR);
    $stmt->execute();
    $pdo_h->commit();
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: index.php");
    exit();
  }catch(Exception $e){
    $_SESSION["MSG"]="loginkeeper登録失敗。";
    log_writer("login_sql.php",$e);
    $pdo_h->rollBack();
  }
}
//リダイレクト
header("HTTP/1.1 301 Moved Permanently");
header("Location: login.php");
exit();
?>