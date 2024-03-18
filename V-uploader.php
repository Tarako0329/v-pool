<?php
	require "php_header.php";
	if(empty($_SESSION["uid"])){
		$_SESSION["MSG"]="セッション切れです。再度ログインしてください。";
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: login.php");
		exit();
	}
	$token = csrf_create();
   //var_dump(getFileList("./upload/ryota/"));
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
	<HEADER class='text-center' style='color:#FFA400' id='header'>
		<h1><a href='/' class='a_none'>Video Uploader</a></h1>
		<div class='youkoso'><?php echo "ID:".$_SESSION["uid"];?></div>
	</HEADER>
	<MAIN class='container' style='color:#fff;padding-bottom:0;'>
		<div id='getlist'>
			<div id='uploadarea'>
				<transition>
					<div v-show="msg!==''" class="alert alert-warning" role="alert">
					  {{msg}}
					</div>
				</transition>

				<div class='row' style='margin-bottom:15px;' ><!--動画選択-->
				<div class='col-1'></div>
				<div class='col-10'>
				<label for='formFile' class='form-label' >アップロードする動画を選択（複数可）</label>
				<label class='btn btn-primary btn-lg' style='width:100%' >動画選択
					<input type='file' class='form-control btn-lg' style='display:none;' id='formFile' @click='selectfiles($event)' multiple>
				</label>
				</div>
				<div class='col-1'></div>
				</div><!--動画選択-->
				<div v-show='["fileset","stop"].includes(stats)' class='row'><!--送信ボタン-->
				<div class='col-1'></div>
				<div class='col-10'>
					<select class='form-select' placeholder='アップロードフォルダの指定' v-model='upfolder'>
						<template v-for='(list,index) in tree' :key='list.level'>
							<option :value='list.level'>{{list.fullLvName}}</option>
						</template>
					</select>
				</div>
				<div class='col-1'></div>

				<div class='col-1'></div>
				<div class='col-10'>
					<button type='button' class='btn btn-primary btn-lg' style='width:50%' @click="uploading()">送 信</button>
					<button type='button' class='btn btn-warning btn-lg' style='width:50%' @click="cancel()">キャンセル</button>
				</div>
				<div class='col-1'></div>
				</div><!--送信ボタン-->
				<div v-show='["sending","stop"].includes(stats)' class='row fadein'><!--再開・停止ボタン-->
				<div class='col-1'></div>
				<div class='col-10'>
					<button type='button' class='btn btn-warning btn-lg' style='width:50%' @click="cancel()">キャンセル</button>
					<button type='button' class='btn btn-success btn-lg' style='width:50%' @click="pause()">一時停止</button>
					<button type='button' class='btn btn-success btn-lg' style='width:50%' @click="resume()">再開</button>
				</div>
				<div class='col-1'></div>
				</div><!--再開・停止ボタン-->
				<div v-show='["error"].includes(stats)' class='row fadein'><!--リトライ-->
				<div class='col-1'></div>
				<div class='col-10'>
					<button type='button' class='btn btn-success btn-lg' style='width:50%' @click="retry()">リトライ</button>
				</div>
				<div class='col-1'></div>
				</div><!--リトライ-->
				<div v-show='["sending","stop"].includes(stats)' class='row' style='margin-bottom:15px;'><!--progressbar-->
				<div class='col-3 text-end'>{{result}}</div>
					<div class='col-8' style='padding:2px 12px 2px 2px;'>
						<div style='border:solid 1px #FFA400;height:100%;width:100%;'>
							<div class='text-center' style='height:100%;width:0%;padding:0;margin:0;background-color:#fff;color:#FFA400;' id='progressbar'>0%</div>
						</div>
					</div>
				<div class='col-1'></div>
				</div><!--progressbar-->
				<div class='container fadein' id='filelist' style='border:solid 1px #FFA400;margin-bottom:15px;height: 0px;overflow: auto;'  scrolling="no"><!--ここにファイルリスト表示-->
				<template v-for='(file,index) in filelist' :key='file.name'>
					<div class='row'><div class="col-1"></div><div class="col-8">{{file.name}}</div><div class="col-2" :id="index">{{file.persent}}</div><div class="col-1"></div></div>
				</template>
				<!--{{filelist}}-->
				</div><!--ここにファイルリスト表示-->
				<hr>
				<div class='row text-center' style='margin-bottom:0;'><h3>未分類動画一覧</h3></div>
			</div>
		</div>

		<!--<div style='width:100%;height:50%;' id='Vmanager'>
			<iframe src="V-manager-iframe.php?lv=0%" width="100%" height="100%" id='Vmanager-frame'></iframe>
		</div>-->

		<div id='Vmanager'><!--Vmanager-->
			<div class='v-div-header' style='position: relative;top:0px;'>
				<div id='folderopen' style="" @click='foldersetOpen("","","disp")' role="button" data-bs-toggle='modal' data-bs-target='#folderediter'>フォルダ選択・作成 <i class="bi bi-folder2-open h3 treei"></i></div>
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

			<div class='modal fade' id='folderediter' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'><!--モーダル-->
				<div class='modal-dialog  modal-dialog-centered modal-dialog-scrollable'>
					<div class='modal-content edit' style='color:black;'>
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
		</div><!--Vmanager-->

	</MAIN>
	
	<script>//vue.js
		V_uploader('V-uploader.php','%','<?php echo $token;?>').mount('#getlist');
		V_manager('V-uploader.php','0%','<?php echo $token;?>').mount('#Vmanager');
	</script><!--vue-->


</BODY>
</html>









