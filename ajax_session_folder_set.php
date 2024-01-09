<?php
  require_once "php_header.php";
  if(empty($_GET["level"])){
    exit();
  }

  $_SESSION["level"] = $_GET["level"];

  exit();
?>