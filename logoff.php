<?php
  require "php_header.php";
  setCookie("vpool", '', -1, "/", "", TRUE, TRUE); // secure, httponly
  $_SESSION=[];
  $_SESSION["msg"]="ログオフしました";
 
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: login.php");
  exit();
?>