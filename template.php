<?php
    require "php_header.php";
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head_bs5.php" 
    ?>
    <script src="./script/flow.js"></script>
    <TITLE>Video Uploader</TITLE>
</head>
<BODY id = 'body' style='background:black;' >
    <HEADER class='container text-center' style='color:#FFA400'>
        <h1>Video Uploader</h1>
    </HEADER>
    <MAIN class='container' style='color:#fff;'>
    </MAIN>
    <FOOTER>
    </FOOTER>
    <script>
      const { createApp, ref, onMounted, computed } = Vue;
      createApp({
        setup() {
          onMounted(()=>{

          })

          return{

          }
        }
      }).mount('#getlist');
    </script>
</BODY>
</html>