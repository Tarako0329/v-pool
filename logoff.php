<?php
  require "php_header.php";
  setCookie("vpool", '', -1, "/", "", false, TRUE); // secure, httponly
  $_SESSION=[];
  $_SESSION["MSG"]="ログオフしました";
 
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: login.php");
  exit();
?>