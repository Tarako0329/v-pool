<?php
require_once "php_header.php";

$config = new \Flow\Config();
//$config->setTempDir( SAVEDIR.$_SESSION["uid"].'/chunks_temp_folder'); //小分けファイルの一時保存先指定
$config->setTempDir( "./upload/".$_SESSION["uid"].'/chunks_temp_folder'); //小分けファイルの一時保存先指定


$file = new \Flow\File($config);
$request = new \Flow\Request();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($file->checkChunk()) {
    header("HTTP/1.1 200 Ok");
    log_writer("upload.php","HTTP/1.1 200 Ok");
    log_writer("upload.php \$file->checkChunk()",$file->checkChunk());
  } else {
    header("HTTP/1.1 204 No Content");
    //header("HTTP/1.1 400 Bad Request");
    log_writer("upload.php","HTTP/1.1 204 No Content");
    //log_writer("upload.php \$GET",$_GET);
    //log_writer("upload.php \$POST",$_POST);
    //log_writer("upload.php \$file",$file);
    log_writer("upload.php \$file->checkChunk()",$file->checkChunk());
    return ;
  }
} else {
  if ($file->validateChunk()) {
    $file->saveChunk();
    log_writer("upload.php","\$file->saveChunk()");
  } else {
    // error, invalid chunk upload request, retry
    header("HTTP/1.1 400 Bad Request");
    log_writer("upload.php","HTTP/1.1 400 Bad Request");
    return ;
  }
}
$savedir = SAVEDIR.$_SESSION["uid"].'/temp/';
$filenname = date('YmdHis')."-".$request->getFileName();
log_writer("upload.php",$savedir.$filenname);

//ファイルが揃ったら結合して保存
if ($file->validateFile() && $file->save( $savedir.$filenname) ) {
  // ファイルが全部アップロードされた後の処理
  $sql = "insert into filelist(uid,filename,before_name) values(:id,:filename,:before_name)";
  $stmt = $pdo_h->prepare($sql);
  $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("filename", $filenname, PDO::PARAM_STR);
  $stmt->bindValue("before_name", $request->getFileName(), PDO::PARAM_STR);
  $stmt->execute();

}else{
  // ファイルアップロード途中のときの処理

}
?>