<?php
	require "php_header.php";
	
	if(empty($_SESSION["uid"])){
		$_SESSION["MSG"]="セッション切れです。再度ログインしてください。";
	  header("HTTP/1.1 301 Moved Permanently");
	  header("Location: login.php");
	  exit();
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
	<div id = 'app' style='width:100%;height:100%;'>
	<HEADER class='text-center' style='color:#FFA400;height:130px;'>
		<h1>Video Uploader</h1>
		<div class='youkoso'><?php echo "user:".$_SESSION["name"];?></div>
		<div class='logoff'><a href="logoff.php">logoff</a></div>
		<div class='row'><!--送信ボタン-->
			<div class='col-1'></div>
			<div class='col-10'>
				<form action='V-uploader.php' style='width:100%'>
					<button class='btn btn-primary btn-lg' style='width:100%'>動画アップロード</button>
				</form>
			</div>
			<div class='col-1'></div>
		</div><!--送信ボタン-->
	</HEADER>
	<MAIN class='container' style='color:#fff;padding-top:130px;'>
		<div style='width:100%;height:100%;' id='Vmanager'>
      <iframe src="V-manager-iframe.php?lv=%" width="100%" height="100%" id='Vmanager-frame'></iframe>
    </div>

	</MAIN>
	<FOOTER>
		
	</FOOTER>
	</div>
	<script>
		const { createApp, ref, onMounted, computed } = Vue;
		createApp({
			setup() {
				const message = ref('clear');
				
				onMounted(() => {
					console.log("onMounted")
				});

				return {
					message,
				};
			}
		}).mount('#app');
	</script><!--vue-->
	<script>
 
	</script>


</BODY>
</html>









