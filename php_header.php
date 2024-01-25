<?php
date_default_timezone_set('Asia/Tokyo'); 
//ini_set('max_execution_time', -1);
//ini_set('max_input_time', -1);
require "./vendor/autoload.php";
//.envの取得
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
define("MAIN_DOMAIN",$_ENV["MAIN_DOMAIN"]);
define("ROOT_URL",$_ENV["HTTP"]);

//$rtn=session_set_cookie_params(24*60*60*24*3,'/',MAIN_DOMAIN,true,true);
session_start();
//$_SESSION = [];

require "functions.php";

if(MAIN_DOMAIN==="localhost:81"){
  $time=date('Ymd-His');
  $id="demo";
  $pass="00000000";
}else{
  $time=date('Y')."up03";
  $id="";
  $pass="";
}


//$pass=dirname(__FILE__);


// DBとの接続
define("DNS","mysql:host=".$_ENV["SV"].";dbname=".$_ENV["DBNAME"].";charset=utf8");
define("USER_NAME", $_ENV["USER"]);
define("PASSWORD", $_ENV["PASS"]);
$pdo_h = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());

define("SAVEDIR", $_ENV["SAVEDIR"]);
define("NOM", $_ENV["SIO"]);

if(!empty($_GET["v"])){
  setCookie("vpool", $_GET["v"], time()+60*60*24*7, "/", "",true,true);
  $token = $_GET["v"];
}else{
  $token = !empty($_COOKIE["vpool"])?$_COOKIE["vpool"]:"";
}

if($_SESSION["MSG"] <> "ログオフしました"){
  if(!empty($_SESSION["uid"])){
    //ログイン継続・期間延長

    setCookie("vpool", $token, time()+60*60*24*7, "/", "", TRUE, TRUE);//1week
    log_writer("login延長",time()+60*60*24*7);
    try{
      $pdo_h->beginTransaction();
      $sql = "update loginkeeper set keepdatetime =:kdatetime where uid =:id and token =:token)";
      $stmt = $pdo_h->prepare($sql);
      $stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
      $stmt->bindValue("token", $_COOKIE["vpool"], PDO::PARAM_STR);
      $stmt->bindValue("kdatetime", strtotime("+7 day"), PDO::PARAM_STR);
      $stmt->execute();
      $pdo_h->commit();
    }catch(Exception $e){
      $_SESSION["MSG"]="loginkeeper延長登録失敗。";
      $pdo_h->rollBack();
    }
  }else{
    //トークンからuidを取得
    log_writer("トークンからuidを取得",$_SESSION);
    $sql = "select * from loginkeeper where token =:token and keepdatetime >=:kdatetime";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("token", $token, PDO::PARAM_STR);
    $stmt->bindValue("kdatetime", date("Y-m-d"), PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetchAll();
    if(!empty($user[0]["uid"])){
      $_SESSION["uid"] = $user[0]["uid"];
      $_SESSION["name"] = "hoge";
    }else{
      $_SESSION["MSG"]="ログイン有効期限切れです。再ログインしてください。";
    }
  }
}
?>