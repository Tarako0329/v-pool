<?php
require "php_header.php";

$sql = "select * from filelist where uid = :id order by insdate desc";
$stmt = $pdo_h->prepare($sql);
$stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
$stmt->execute();



//jsonとして出力
header('Content-type: application/json');
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
?>