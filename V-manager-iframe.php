<?php
    require "php_header.php";
	if(empty($_SESSION["uid"])){
        $_SESSION["MSG"]="セッション切れです。再度ログインしてください。";
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: login.php");
        exit();
    }
   //var_dump(getFileList("./upload/ryota/"));
?>
<!DOCTYPE html>
<html lang='ja'>

<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head_bs5.php" 
    ?>
</head>
<BODY id = 'body' style='background:black;' >
    <form action='update.php' method="post" id='getlist'>
    <MAIN class='container' style='color:#fff;padding:0;' >
        <hr>
        <div id='mibunrui'><!--動画一覧-->
            <div class='row text-center'><h3>未分類動画一覧</h3></div>
            <div class='row'>
            <template v-for='(file,index) in files' :key='file.fileNo'>
                <div class ='col-4 col-lg-2' style='margin-bottom:20px;'>
			        <video style='max-width:100%;width:100%;'preload='metadata' controls muted :src='`./upload/${file.uid}/${file.filename}#t=0.01`'></video>
	    		</div>
                <div class ='col-8 col-lg-4' style='margin-bottom:20px;' :id='`File_NO_${index}`'>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" :id="index" :name='`list[${index}][upd]`'>
                        <label class="form-check-label" :for="index">一括更新対象</label>
                    </div>
                    <p style='color:#fff;margin-bottom: 4px;'>ファイル名：{{file.before_name}}</p>
                    <p style='color:#fff;margin-bottom: 4px;'>保存日時：{{file.insdate}}</p>
                    <label class="form-check-label" :for='`list[${index}][titel]`' style='color:#fff;'>タイトル：</label>
                    <input type='text' class="form-control" :value=file.titel :name='`list[${index}][titel]`' :id='`list[${index}][titel]`'>
                    <label class="form-check-label" :for='`list[${index}][name]`' style='color:#fff;'>分類：</label>
                    <input type='text' class="form-control" :value=file.name :name='`list[${index}][name]`' :id='`list[${index}][name]`' placeholder="例：2020年/5月/運動会">
                    <label class="form-check-label" :for='`list[${index}][tags]`' style='color:#fff;'>タグ：</label>
                    <input type='text' class="form-control" :value=file.tags :name='`list[${index}][tags]`' :id='`list[${index}][tags]`' placeholder="例：#子供#運動会">
                    
                    <input type='hidden' :value="file.fileNo" :name='`list[${index}][fileNo]`'>
                </div>
            </template>
            </div>
        </div><!--動画一覧-->
    </MAIN>
    <FOOTER>
        <div class='row'>
            <div class='col-1'></div>
            <div class='col-10'>
                <button class='btn btn-primary btn-lg' style='width:100%' >更 新</button>
            </div>
            <div class='col-1'></div>
        </div>
    </FOOTER>
    </form>
    <script>//vue.js
        const { createApp, ref, onMounted, reactive,computed } = Vue;
        createApp({
            setup() {
                const files = ref()
                const get_files = () => {//アップロード後の分類等未設定の動画一覧を取得
                    axios
                    .get('ajax_getnoinfofiles.php')
                    .then((response) => {
                        files.value = [...response.data],
                        console_log('get_files succsess')
                        //console_log(files.value)
                    })
                    .catch((error) => console.log(error));
                }
                onMounted(() => {
                    get_files()
                });

                
                return {
                    files,
                    get_files,
                };
            }
        }).mount('#getlist');
    </script><!--vue-->


</BODY>
</html>









