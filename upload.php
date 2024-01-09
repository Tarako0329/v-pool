<?php
require_once "php_header.php";

$_SESSION["level"] = empty($_SESSION["level"])?"0000000000":$_SESSION["level"];
log_writer("\$level",$_SESSION["level"]);

$config = new \Flow\Config();
$config->setTempDir( SAVEDIR.$_SESSION["uid"].'/chunks_temp_folder'); //小分けファイルの一時保存先指定

$file = new \Flow\File($config);
$request = new \Flow\Request();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($file->checkChunk()) {
    header("HTTP/1.1 200 Ok");
  } else {
    header("HTTP/1.1 204 No Content");
    exit() ;
  }
} else {
  if ($file->validateChunk()) {
    $file->saveChunk();
  } else {
    // error, invalid chunk upload request, retry

    $loadfile = (new \Flow\Request())->getFile();
    $messages='UPLOAD_ERR_UNKNOWN';
    switch($loadfile['error']){
        case UPLOAD_ERR_INI_SIZE:
            // upload_max_filesize ディレクティブの値を超えている
            $messages = 'UPLOAD_ERR_INI_SIZE';
            break;

        case UPLOAD_ERR_FORM_SIZE:
            // HTMLで指定されたMAX_FILE_SIZE を超えている
            $messages = 'UPLOAD_ERR_FORM_SIZE';
            break;

        case UPLOAD_ERR_PARTIAL:
            // 一部のみしかアップロードされた
            $messages = 'UPLOAD_ERR_PARTIAL';
            break;

        case UPLOAD_ERR_NO_FILE:
            // アップロードされなかった（ファイルが無い）
            $messages = 'UPLOAD_ERR_NO_FILE';
            break;

        case UPLOAD_ERR_NO_TMP_DIR:
            // テンポラリフォルダがない
            $messages= 'UPLOAD_ERR_NO_TMP_DIR';
            break;
        case UPLOAD_ERR_CANT_WRITE:
            // 書き込みに失敗
            $messages= 'UPLOAD_ERR_CANT_WRITE';
            break;

        case UPLOAD_ERR_EXTENSION:
            // 拡張モジュールがファイルのアップロードを中止した
            $messages= 'UPLOAD_ERR_EXTENSION';
            break;
    }
    header("HTTP/1.1 400 Bad Request");
    echo $messages;
    exit() ;
  }
}
$savedir = SAVEDIR.$_SESSION["uid"].'/';
$filenname = date('YmdHis')."-".$request->getFileName();
//log_writer("upload.php \$request",$request);

//ファイルが揃ったら結合して保存
if ($file->validateFile() && $file->save( $savedir.$filenname) ) {
  // ファイルが全部アップロードされた後の処理
  $sql = "insert into filelist(uid,filename,before_name,level) values(:id,:filename,:before_name,:level)";
  $stmt = $pdo_h->prepare($sql);
  $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("filename", $filenname, PDO::PARAM_STR);
  $stmt->bindValue("before_name", $request->getFileName(), PDO::PARAM_STR);
  $stmt->bindValue("level", $_SESSION["level"], PDO::PARAM_STR);
  $stmt->execute();

}else{
  // ファイルアップロード途中のときの処理

}

?>