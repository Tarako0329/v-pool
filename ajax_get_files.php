<?php
  require "php_header.php"; 
  /*
  $lv = $_GET[""];
  $lv = $_GET[""];
  $lv = $_GET[""];
  $lv = $_GET[""];
  */
  $lv = $_GET["lv"];

  $sql = "select fl.*,lv.name,lv.fullLvName 
  from filelist as fl 
  left join levels as lv on fl.level=lv.level and  fl.uid=lv.uid 
  where fl.uid = :id and fl.level like :lv order by fl.insdate desc";
  /*
  $sql = "select 
      *
    from filelist
    where 
      uid = :id 
      and level like :lv
    order by insdate desc";
  */
  $stmt = $pdo_h->prepare($sql);
  $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("lv",$lv , PDO::PARAM_STR);
  /*
  $stmt->bindValue("level","0000000000" , PDO::PARAM_STR);
  $stmt->bindValue("title",null , PDO::PARAM_STR);
  $stmt->bindValue("tags",null , PDO::PARAM_STR);
  */
  $stmt->execute();
  $filelists = $stmt->fetchAll(PDO::FETCH_ASSOC);
  //jsonとして出力
  header('Content-type: application/json');
  echo json_encode($filelists, JSON_UNESCAPED_UNICODE);

?>