<?php
date_default_timezone_set('Asia/Tokyo');
//ini_set('max_execution_time', -1);
//ini_set('max_input_time', -1);
require "./vendor/autoload.php";
//.envの取得
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
define("MAIN_DOMAIN",$_ENV["MAIN_DOMAIN"]);
define("ROOT_URL","https://".MAIN_DOMAIN."/");

session_start();

$_SESSION["uid"] = "demo";

require "functions.php";

$time=date('Ymd-His');

$pass=dirname(__FILE__);


// DBとの接続
define("DNS","mysql:host=".$_ENV["SV"].";dbname=".$_ENV["DBNAME"].";charset=utf8");
define("USER_NAME", $_ENV["USER"]);
define("PASSWORD", $_ENV["PASS"]);
$pdo_h = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());

define("SAVEDIR", $_ENV["SAVEDIR"]);



?>