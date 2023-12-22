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
		
		<div class='row' id='getlist'>
			<div>検索：<input type="text" v-model="search" /></div>
			
			<template  v-for='file in updated_users'>
				<div v-if=file.type==='directory' class ='col-12'>{{file.src}}</div>
				<div v-if=file.type==='file' class ='col-4 col-md-3 col-lg-2 col-xl-1'>
					<video style='max-width:100%;width:100%;'preload='metadata' controls muted v-bind:src="file.src"></video>
					<p style='color:#fff;'>{{file.name}}</p>
				</div>
			</template>
			<div ref="observe_element">この要素を監視します</div>
		</div>

	</MAIN>
	<FOOTER>
		
	</FOOTER>
	<script>
		const { createApp, ref, onMounted, computed } = Vue;
		createApp({
			setup() {
				const message = ref('clear');
				const files = ref([])
				const search = ref('')
				const observe_element = ref(null)
				
				onMounted(() => {
					get_files('true')
					console.log(observe_element.value)
					const observer = new IntersectionObserver(entries => {
						const entry = entries[0]
						if (entry && entry.isIntersecting) {
							console.log('画面に入ったよ')
							get_files('false')
						}
					})
					observer.observe(observe_element.value)
				});

				const updated_users = computed(() => {
				  let searchWord = search.value.toString().trim();
				  if (searchWord === "") return files.value;
				
				  return files.value.filter((file) => {
					return (
					  file.type.includes(searchWord) ||
					  file.src.includes(searchWord) ||
					  file.name.includes(searchWord)
					);
				  });
				});
				
				const get_files = (first) => {
					axios
					.get('ajax_loader.php?name=eriko&iCount=3&first=' + first)
					.then((response) => (files.value = [...files.value,...response.data],
										console.log('setup succsess')
										//,console.log(files))
										))
					.catch((error) => console.log(error));
				}
				
				const clear = () => {
					files.value = ([])
					axios
						.get('ajax_loader.php?name=eriko')
						.then((response) => (
							files.value = response.data
							//,console.log(files)
							))
						.catch((error) => console.log(error));
				}
				
				return {
					message,
					files,
					search,
					updated_users,
					clear,
					observe_element,
					get_files,
				};
			}
		}).mount('#getlist');
	</script><!--vue-->
	<script>
 
	</script>


</BODY>
</html>









