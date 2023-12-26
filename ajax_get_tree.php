<?php
  require "php_header.php";
  $a="a";
  //log_writer('\$_SESSION["uid"]',++$a);
  $sql = "select 
            if(level='0000000000',0, replace(level,'0','')) as lv 
            ,level
            ,name
            ,concat(length(if(level='0000000000',0, replace(level,'0',''))) * 10,'px') as padding
            ,concat((length(if(level='0000000000',0, replace(level,'0','')))+1) * 10,'px') as next_padding
            ,'none' as newfolder
            ,'' as newname 
          from levels where uid = :id order by level";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

  header('Content-type: application/json');  
  echo json_encode($dataset, JSON_UNESCAPED_UNICODE);
  exit();
?>
