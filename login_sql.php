<?php
require "php_header.php";
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
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: index.php");
      exit();
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

      //リダイレクト
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: index.php");
      exit();
    }catch(Exception $e){
      $_SESSION["MSG"]="登録に失敗しました。ユーザーIDがすでに登録されてます。";
      $pdo_h->rollBack();
    }

  }else{

  }
}
//リダイレクト
header("HTTP/1.1 301 Moved Permanently");
header("Location: login.php");
exit();
?>