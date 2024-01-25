<?php
  require "php_header.php";
  $return_satas = "success";

  $rtn = csrf_checker(["V-manager-iframe.php"],["G","C"]);
  if($rtn !== true){
    $return_satas = "error:".$rtn;
  }else if(!empty($_GET)){
    $token = csrf_create();
    //log_writer('\$_GET',$_GET);
    {//同一フォルダのチェック
      if($_GET["lv"]==="0"){
        $ckLv = "_000000000";
        $notlv = "";
        $start_point = 0;
      }else{
        $ckLv = $_GET["lv"]."%";
        $notlv = $_GET["lv"]."0%";
        $start_point = strlen($_GET["lv"]);
        $upperLv = substr($_GET["lv"]."000000000",0,10);
        log_writer('\$upperLv',$upperLv);
      }
      $sql = "select * from levels where uid = :id and level like :lv and name =:name and level not like :notlv";
      $stmt = $pdo_h->prepare($sql);
      $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
      $stmt->bindValue("lv", $ckLv, PDO::PARAM_STR);
      $stmt->bindValue("notlv", $notlv, PDO::PARAM_STR);
      $stmt->bindValue("name", $_GET["name"], PDO::PARAM_STR);
      $stmt->execute();
      $dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
      //log_writer('\$dataset',$dataset);
      if(!empty($dataset)){
        $return_satas = "error:同じ名称のフォルダが存在してます。";
      }
    }
    {//フォルダ上限のチェック
      if($return_satas === "success"){
        $sql = "select (level) as maxlv,name from levels where uid = :id and level like :lv order by level desc";
        $stmt = $pdo_h->prepare($sql);
        $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
        $stmt->bindValue("lv", $ckLv, PDO::PARAM_STR);
        $stmt->execute();
        $dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(next_char(substr($dataset[0]["maxlv"],$start_point,1))!=="error"){
          $lvel = substr($dataset[0]["maxlv"],0,($start_point)).next_char(substr($dataset[0]["maxlv"],$start_point,1)).substr("0000000000",($start_point + 1));  
        }else{
          $return_satas = "error:フォルダ数の上限に達しました";
        }
      }
      log_writer('\$lvel',$dataset[0]["maxlv"]);
      log_writer('\$lvel',$lvel);
    }
    if($return_satas === "success"){
      if($_GET["lv"]!=="0"){//上位フォルダのフルパス名取得
        $sql = "select fullLvName from levels where uid = :id and level = :upperLv";
        $stmt = $pdo_h->prepare($sql);
        $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
        $stmt->bindValue("upperLv", $upperLv, PDO::PARAM_STR);
        $stmt->execute();
        $dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $fullLvName = $dataset[0]["fullLvName"]." > ".$_GET["name"];
      }else{
        $fullLvName = $_GET["name"];
      }
      log_writer('\$fullLvName',$fullLvName);
      try{
        $pdo_h->beginTransaction();
        $sql = "insert into levels(uid,level,name,fullLvName) values(:id ,:lv ,:name ,:fullLvName)";
        $stmt = $pdo_h->prepare($sql);
        $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
        $stmt->bindValue("lv", $lvel, PDO::PARAM_STR);
        $stmt->bindValue("name", $_GET["name"], PDO::PARAM_STR);
        $stmt->bindValue("fullLvName", $fullLvName, PDO::PARAM_STR);
        $stmt->execute();
        $pdo_h->commit();
      }catch(Exception $e){
        $pdo_h->rollBack();
        $return_satas = "error:".$e;
      }
  
    }
  }else{

  }
  $msg = array(
    "status" => $return_satas
    ,"token" => $token
  );


  header('Content-type: application/json');
  echo json_encode($msg, JSON_UNESCAPED_UNICODE);
  exit();
  //header("HTTP/1.1 200 Ok");
  //echo $return_satas;

?>
