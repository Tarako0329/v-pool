<?php
date_default_timezone_set('Asia/Tokyo');
//ini_set('max_execution_time', -1);
//ini_set('max_input_time', -1);
require "./vendor/autoload.php";
//.envの取得
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
define("MAIN_DOMAIN",$_ENV["MAIN_DOMAIN"]);
define("ROOT_URL","http://".MAIN_DOMAIN."/");

/*
$rtn=session_set_cookie_params(24*60*60*24*3,'/','.'.MAIN_DOMAIN,true);
if($rtn==false){
    echo "ERROR:session_set_cookie_params";
    exit();
}
*/
session_start();

$_SESSION["uid"] = "demo";

require "functions.php";

$time=date('Ymd-His');
/*
if(EXEC_MODE=="Test"){
    //テスト環境はミリ秒単位
    //$time="8";
    $time=date('Ymd-His');
    error_reporting( E_ALL );
}else{
    //本番はリリースした日を指定
    $time="20221018-01";
    //$time=date('Ymd');
    error_reporting( E_ALL & ~E_NOTICE );
}
*/

$pass=dirname(__FILE__);


// DBとの接続
define("DNS","mysql:host=".$_ENV["SV"].";dbname=".$_ENV["DBNAME"].";charset=utf8");
define("USER_NAME", $_ENV["USER"]);
define("PASSWORD", $_ENV["PASS"]);
$pdo_h = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());



//deb_echo("端末ID：".MACHIN_ID);


?>




