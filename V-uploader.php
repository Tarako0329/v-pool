<?php
    require "php_header.php";
	if(empty($_SESSION["uid"])){
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
    <script src="./script/flow.js"></script>
    <TITLE>Video Uploader</TITLE>
</head>
<BODY id = 'body' style='background:black;' >
    <HEADER class='text-center' style='color:#FFA400'>
        <h1>Video Uploader</h1>
    </HEADER>
    <form action='update.php' method="post" id='getlist'>
    <MAIN class='container' style='color:#fff;' >
        <div class='row' style='margin-bottom:15px;' ><!--動画選択-->
            <div class='col-1'></div>
            <div class='col-10'>
                <label for='formFile' class='form-label' >アップロードする動画を選択（複数可）</label>
                <!--<button class='btn btn-primary btn-lg' style='width:100%' id='formFile' @click='selectfiles($event)'>動画選択</button>-->
                <label class='btn btn-primary btn-lg' style='width:100%' >動画選択
                    <input type='file' class='form-control btn-lg' style='display:none;' id='formFile' @click='selectfiles($event)' multiple>
                </label>
            </div>
            <div class='col-1'></div>
        </div><!--動画選択-->
        <div class='row' id='filelist' style='margin-bottom:15px;'>
            <!--ここにファイルリスト表示-->
        </div>
        <div class='row' style='margin-bottom:15px;'><!--progressbar-->
            <div class='col-1'></div>
            <div class='col-10' style='border:solid 1px #FFA400;padding:2px;'>
                <div class='text-center' style='height:100%;width:0%;padding:0;margin:0;background-color:#fff;color:#FFA400;' id='progressbar'>0%</div>
            </div>
            <div class='col-1'></div>
        </div><!--progressbar-->
        <div class='row'><!--送信ボタン-->
            <div class='col-1'></div>
            <div class='col-10'>
                <button type='button' class='btn btn-primary btn-lg' style='width:100%' @click="uploading()">送 信</button>
            </div>
            <div class='col-1'></div>
        </div><!--送信ボタン-->
        <hr>
        <div id='mibunrui'>
            <div class='row text-center'><h3>未分類動画一覧</h3></div>
            <!--<div class='row'>
                <div class='col-4 text-center'><button class='btn btn-primary btn-lg' style='width:100%' @click='get_files()'>Titel</button></div>
                <div class='col-4 text-center'><button class='btn btn-primary btn-lg' style='width:100%'>Tree</button></div>
                <div class='col-4 text-center'><button class='btn btn-primary btn-lg' style='width:100%'>Tags</button></div>
            </div>-->
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
                    <p style='color:#fff;margin-bottom: 8px;'>ファイル名：{{file.before_name}}</p>
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
        </div>
    </MAIN>
    <FOOTER>
        <div class='row'><!--送信ボタン-->
            <div class='col-1'></div>
            <div class='col-10'>
                <button class='btn btn-primary btn-lg' style='width:100%' @click="update()">更 新</button>
            </div>
            <div class='col-1'></div>
        </div><!--送信ボタン-->
    </FOOTER>
    </form>
    <script>//vue.js
        const { createApp, ref, onMounted, reactive } = Vue;
        createApp({
            setup() {
                const files = ref()

                var flow = new Flow({
                    target:'upload.php',
                    chunkSize:1*1028*1028*10, //チャンクサイズ（小分けにするサイズです）
                });
                var flowfile= flow.files;
                var index=0

                const selectfiles =(e)=>{
                    console_log('selectfiles')
                    let elem = document.getElementById(e.target.id)
                    flow.assignBrowse(elem)
                }
                const uploading = () =>{
                    console_log("アップロード実行")
                    flow.upload();
                    index=0
                }
                const get_files = () => {
                    axios
                    .get('ajax_getnoinfofiles.php')
                    .then((response) => {
                        files.value = [...response.data],
                        console_log('get_files succsess')
                        console_log(files.value)
                    })
                    .catch((error) => console.log(error));
                }
                flow.on('fileAdded', function(file, event){
                    index = index + Number(1)
                    document.getElementById('filelist').innerHTML += '<div class="col-1"></div><div class="col-8">' + file.name + '</div><div class="col-2" id="' + index + '">0%</div><div class="col-1"></div>'
                    console_log(file);
                });
                flow.on('filesSubmitted', function(file) {
                    // アップロード実行
                    console_log("アップロード準備OK")
                });
                flow.on('progress',function(){
                    //プログレスバーの実行
                    //flow.progress() で進捗が取得できるのでそれを利用してプログレスバーを設定
                    document.getElementById("progressbar").innerHTML = Math.floor(flow.progress()*100) + '%'
                    document.getElementById("progressbar").style.width = Math.floor(flow.progress()*100) + '%'
                    if(flow.progress()===1){
                        document.getElementById("progressbar").style.backgroundColor="#FFA400"
                        document.getElementById("progressbar").style.color="#fff"
                    }
                });
                flow.on('fileSuccess', function(file,message){
                    // アップロード完了したときの処理
                    console_log(`${file.name} アップロード完了`);//今回はメッセージを表示します。
                    index = index + Number(1)
                    document.getElementById(index).innerHTML = '100%'
                });
                flow.on('complete',(file)=>{
                    console_log("アップロードおしまい")
                    flow.cancel()
                    index=0
                    document.getElementById('filelist').innerHTML = ''
                    get_files()
                })

                const update = () =>{
                    console_log('update start')
                    console_log(document.getElementById('mibunrui'))
                }

                onMounted(() => {
                    get_files()
                });

                
                return {
                    files,
                    uploading,
                    selectfiles,
                    get_files,
                    update,
                };
            }
        }).mount('#getlist');
    </script><!--vue-->


</BODY>
</html>









