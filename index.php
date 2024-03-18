<?php
	require "php_header.php";
	
	if(empty($_SESSION["uid"])){
		//$_SESSION["MSG"]=empty($_SESSION["MSG"])?"ログインIDとパスワードを入力し、新規登録をお願いします。":$_SESSION["MSG"];
	  header("HTTP/1.1 301 Moved Permanently");
	  header("Location: login.php");
	  exit();
	}
	$token = csrf_create();
	setCookie("vpool_usage", "on", time()+60*60*24*1825, "/", "",true,true);
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
	<?php 
	//共通部分、bootstrap設定、フォントCND、ファビコン等
	include "head_bs5.php" 
	?>
	<script src="./script/flow.js"></script>
	<script src="./script/vue3.js"></script>
	
	<TITLE>Video Uploader</TITLE>
</head>
<BODY id = 'body' style='background:black;' >
	<div id = 'app' style='width:100%;height:100%;'>
	<HEADER class='text-center' style='color:#FFA400;height:130px;'>
		<h1>Video Uploader</h1>
		<div class='youkoso'><?php echo "ID:".$_SESSION["uid"];?></div>
		<div class='logoff'><a href="logoff.php" class='a_none'>logoff</a></div>
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
	<MAIN class='container' style='color:#fff;padding-top:130px;padding-bottom:10px;'>
		<!--<div style='width:100%;height:100%;' id='Vmanager' scrolling="no">
      <iframe src="V-manager-iframe.php?lv=%" width="100%" height="100%" id='Vmanager-frame'></iframe>
    </div>-->
    <div class='v-div-header' style='top:130px;'>
        <div id='folderopen' @click='foldersetOpen("","","disp")' role="button" data-bs-toggle='modal' data-bs-target='#folderediter'>フォルダ選択・作成 <i class="bi bi-folder2-open h3 treei"></i></div>
        <transition>
            <div v-if="msg!==''" class="alert alert-warning" role="alert">
                {{msg}}
            </div>
        </transition>
    </div>

		<div class='container v-div-main' style='color:#fff;padding:35px 0 30px 0;' >
        <hr>
        <div id='mibunrui'><!--動画一覧-->
            <div class='row'>
            <template v-for='(file,index) in fileview' :key='file.fileNo'>
                <div class ='col-4 col-lg-2' style='margin-bottom:20px;' :id = '`top${index}`'>
			        <video style='max-width:100%;width:100%;'preload='metadata' controls muted :src='`./upload/${file.uid}/${file.filename}#t=0.01`'></video>
	    		</div>
                <div class ='col-8 col-lg-4' style='margin-bottom:20px;' :id='`File_NO_${index}`'>
                    <!--未実装<div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" :id="index" :name='`list[${index}][upd]`'>
                        <label class="form-check-label" :for="index">一括更新対象</label>
                    </div>-->
                    <p style='color:#fff;margin-bottom: 4px;'>ファイル名：{{file.before_name}}</p>
                    <p style='color:#fff;margin-bottom: 0px;'>保存日時：{{file.insdate}}</p>

                    <span class="form-check-label" style='color:#fff;'>title：{{file.title}}</span>
                    <button type='button' class='ib' @click='title_change(`${index}title`)' style='margin:0;'><i class="bi bi-pencil-square"></i></button>

                    <div v-if='`${index}title`===title_cg' style='display:flex;height:35px'>
                        <input type='text' class="form-control form-control-sm tree_input" :id='`list[${index}][titel]`' maxlength='40'>
                        <button type='button' class='ib' @click='title_write(index)' style='margin:0;'><i class="bi bi-arrow-return-left"></i></button>
                    </div>
                    <p style='color:#fff;margin-bottom: 4px;'>フォルダ：{{file.fullLvName}}</p>
                    <button type='button' class='ib' @click='foldersetOpen(index,file.fileNo,"mng")' :id='`list[${index}][titel]FB`' data-bs-toggle='modal' data-bs-target='#folderediter'>
                        <i class="bi bi-folder-plus h1"></i>
                    </button>
                    <button type='button' class='ib' @click='filetrash(file.fileNo,file.filename)'><i class="bi bi-trash3 h1"></i></button>

                    <!--未実装<label class="form-check-label" :for='`list[${index}][tags]`' style='color:#fff;'>タグ：</label>
                    <i class="bi bi-hash h1"></i>
                    <input type='text' class="form-control" :value=file.tags :name='`list[${index}][tags]`' :id='`list[${index}][tags]`' placeholder="例：#子供#運動会">-->
                    <input type='hidden' :value="file.fileNo" :name='`list[${index}][fileNo]`'>
                </div>
            </template>
            </div>
            
        </div><!--動画一覧-->
        <div class='row' style='height: 40px;padding:0;'>
            <div class='col-4 text-center fbtn'><a class='a_none' href="#top0" @click='move_page(-6)'>＜＜</a></div>
            <div class='col-4' style='border-left:solid 1px #FFA400;border-right:solid 1px #FFA400;margin:0;'>
            </div>
            <div class='col-4 text-center fbtn'><a class='a_none' href="#top0" @click='move_page(6)'>＞＞</a></div>
        </div>
    </div>

	</MAIN>
	<!--<FOOTER>
	</FOOTER>-->

	<div class='modal fade' id='folderediter' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'><!--モーダル-->
		<div class='modal-dialog  modal-dialog-centered modal-dialog-scrollable'>
			<div class='modal-content edit' style=''>
				<div class='modal-header'>
				</div>
				<div class='modal-body'>
				<template v-for='(list,index) in tree' :key='list.level'>
					<div>
						<div v-if='index===0' :style='{"padding-left":list.padding}' class='treeil'>
							<i class="bi bi-folder-plus h3 treei" style='color:#FFA400;'></i>
							<input class="form-control form-control-sm tree_input" type='text' placeholder="フォルダ名" v-model='list.newname' maxlength='30'>
							<button type='button' class='btn btn-outline-light treeb' @click='ins_tree(index)'>作成</button>
						</div>

						<div v-show='list.kaisou<="1" || tree_tenkai_list.includes(list.upper)' :style='{"padding-left":list.padding}' class='treeil' :id='"li_"+list.level' @click='choese_folder(index)' role='button'>
							<i class="bi bi-folder h3 treei" :id='"i_"+list.level'></i>{{list.name}}
						</div>

						<div v-show='(index!==0 ) && (tree_tenkai_list.includes(list.level))' :style='{"padding-left":list.next_padding}' class='treeil'>
							<template v-if='list.newfolder==="none"' >
								<a href="#" style='color:#FFA400;' @click='foldernameset(index)'><i class="bi bi-folder-plus h3 treei"></i>新規作成</a>
							</template>
							<template v-if='list.newfolder==="display"' >
								<i class="bi bi-folder-plus h3 treei" style='color:#FFA400;'></i>
								<input class="form-control form-control-sm tree_input" type='text' placeholder="フォルダ名" v-model='list.newname' maxlength='30'>
								<button type='button' class='btn btn-outline-light treeb' @click='ins_tree(index)'>作成</button>
							</template>
						</div>

					</div>
				</template>
				</div>
				<div class='modal-footer'>
					<button v-show='folderAreaRole==="mng"' class='btn btn-primary' type='button' @click='foldersetClose()' data-bs-dismiss="modal">決 定</button>
				</div>
			</div>
		</div>
	</div>

	</div>
	<script>
		V_manager('index.php','%','<?php echo $token;?>').mount('#app');
	</script><!--vue-->
	<script>
 
	</script>


</BODY>
</html>









