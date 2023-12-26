<?php
  require "php_header.php";

  //$sql = "select fl.*,lv.name from filelist as fl left join levels as lv on fl.level=lv.level and  fl.uid=lv.uid where fl.uid = :id and (fl.level = :level or fl.title = :title or fl.tags = :tags) order by fl.insdate desc";
  $sql = "select *,concat(lv1name,lv2name,lv3name,lv4name,lv5name,lv6name,lv7name,lv8name,lv9name,lv10name) as fullLvName from filelist_view where uid = :id order by insdate desc";
  $stmt = $pdo_h->prepare($sql);
  $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
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