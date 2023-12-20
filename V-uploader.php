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
    <HEADER class='container text-center' style='color:#FFA400'>
        <h1>Video Uploader</h1>
    </HEADER>
    <MAIN class='container' style='color:#fff;'>
        <div class='row' style='margin-bottom:15px;' ><!--動画選択-->
            <div class='col-1'></div>
            <div class='col-10'>
                <label for='formFile' class='form-label' >アップロードする動画を選択（複数可）</label>
                <button class='btn btn-primary btn-lg' style='width:100%' id='formFile' >動画選択</button>
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
                <button class='btn btn-primary btn-lg' style='width:100%' onclick="uploading()">送 信</button>
            </div>
            <div class='col-1'></div>
        </div><!--送信ボタン-->
        <hr>
        <div class='row' id='getlist'>
            <div class='col-12 text-center'><h3>未分類動画一覧</h3></div>
            <div class='col-4 text-center'><button class='btn btn-primary btn-lg' style='width:100%' @click='get_files()'>Titel</button></div>
            <div class='col-4 text-center'><button class='btn btn-primary btn-lg' style='width:100%'>Tree</button></div>
            <div class='col-4 text-center'><button class='btn btn-primary btn-lg' style='width:100%'>Tags</button></div>
            <template  v-for='file in updated_users'>
            </template>
        </div>
    </MAIN>
    <FOOTER>
        
    </FOOTER>
    <script>//flow.js
        var flow = new Flow({
            target:'upload.php',
            chunkSize:1*1028*1028*10, //チャンクサイズ（小分けにするサイズです）
        });
        var flowfile= flow.files;
        var index=0
        flow.assignBrowse(document.getElementById('formFile'));

        flow.on('fileAdded', function(file, event){
            console_log(file);
            console_log(file.name);
            console_log(file.file.lastModifiedDate);
            index = index + Number(1)
            document.getElementById('filelist').innerHTML += '<div class="col-1"></div><div class="col-8">' + file.name + '</div><div class="col-2" id="' + index + '">0%</div><div class="col-1"></div>'
        });

        flow.on('fileSuccess', function(file,message){
            // アップロード完了したときの処理
            console_log(`${file.name} アップロード完了`);//今回はメッセージを表示します。
            index = index + Number(1)
            document.getElementById(index).innerHTML = '100%'
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
        flow.on('complete',(file)=>{
            console_log("アップロードおしまい")
            flow.cancel()
            index=0
            document.getElementById('filelist').innerHTML = ''
        })
        const uploading = () =>{
            flow.upload();
            index=0
            console_log("アップロード実行")
        }
    </script>
    <script>//vue.js
        const { createApp, ref, onMounted, reactive } = Vue;
        createApp({
            setup() {
                const files = ref()
                
                onMounted(() => {
                });

                const get_files = (first) => {
                    axios
                    .get('ajax_getnoinfofiles.php')
                    .then((response) => {
                        files.value = [...response.data],
                        console_log('get_files succsess')
                        console_log(files.value)
                    })
                    .catch((error) => console.log(error));
                }
                
                return {
                    files,
                    get_files,
                };
            }
        }).mount('#getlist');
    </script><!--vue-->


</BODY>
</html>









