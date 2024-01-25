<?php
  require "php_header.php";
  if($_COOKIE["vpool_usage"] <> "on"){
    $_SESSION["MSG"]="ログインIDとパスワードを入力し、新規登録をお願いします。";
  }
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
    <MAIN class='container' style='color:#fff;' id='app'>
      <div class='row'>
        <div v-if="msg!==''" class="alert alert-warning" role="alert">
          {{msg}}
        </div>

        <form action='login_sql.php' method="POST">
          <label for='id' class='form-label'>ログインID</label>
          <input type='text' class='form-control' name='id' id='id' value='<?php echo $id;?>' maxlength='40' required>
          <label for='pass' class='form-label'>パスワード</label>
          <input type='password' class='form-control' name='pass' id='pass' value='<?php echo $pass;?>' maxlength='10' required>
          <label for='nickname' class='form-label'>ニックネーム</label>
          <input type='text' class='form-control' name='nickname' id='nickname'>
          <div class='row' style='margin-top:10px;'>
            <div class='col-6 text-center'><button type='submin' class='btn btn-primary btn-lg' name='login' value='login'>ログイン</button></div>
            <div class='col-6 text-center'><button type='submin' class='btn btn-primary btn-lg' name='login' value='newlogin'>新規登録</button></div>
          </div>
        </form>
      </div>
    </MAIN>
    <FOOTER>
    </FOOTER>
    <script>
      const { createApp, ref, onMounted, computed } = Vue;
      createApp({
        setup() {
          const msg = ref('<?php echo $_SESSION["MSG"];?>')
          onMounted(()=>{

          })

          return{
            msg,
          }
        }
      }).mount('#app');
    </script>
</BODY>
</html>
<?php
  $_SESSION["MSG"]="";
?>