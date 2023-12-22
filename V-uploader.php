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
    <script src="./script/flow.js"></script>
    <TITLE>Video Uploader</TITLE>
</head>
<BODY id = 'body' style='background:black;' >
    <HEADER class='text-center' style='color:#FFA400'>
        <h1><a href='/'>Video Uploader</a></h1>
        <div class='youkoso'><?php echo "ようこそ".$_SESSION["name"]."さん";?></div>
    </HEADER>
    <form action='update.php' method="post" id='getlist'>
    <MAIN class='container' style='color:#fff;' >
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
        <div class='row fadein' id='filelist' style='border:solid 1px #FFA400;margin-bottom:15px;'><!--ここにファイルリスト表示-->
            <!--ここにファイルリスト表示-->
        </div><!--ここにファイルリスト表示-->
        <div class='row' style='margin-bottom:15px;'><!--progressbar-->
            <div class='col-3 text-end'>{{result}}</div>
            <div class='col-8' style='padding:2px 12px 2px 2px;'>
                <div style='border:solid 1px #FFA400;height:100%;width:100%;'>
                    <div class='text-center' style='height:100%;width:0%;padding:0;margin:0;background-color:#fff;color:#FFA400;' id='progressbar'>0%</div>
                </div>
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
        <div v-if='["sending","stop"].includes(stats)' class='row fadein'><!--再開・キャンセルボタン-->
            <div class='col-1'></div>
            <div class='col-10'>
                <button type='button' class='btn btn-success btn-lg' style='width:50%' @click="resume()">再開</button>
                <button type='button' class='btn btn-warning btn-lg' style='width:50%' @click="cancel()">キャンセル</button>
                <button type='button' class='btn btn-success btn-lg' style='width:50%' @click="pause()">ストップ</button>
            </div>
            <div class='col-1'></div>
        </div><!--再開・キャンセルボタン-->
        <div v-if='["error"].includes(stats)' class='row fadein'><!--リトライ-->
            <div class='col-1'></div>
            <div class='col-10'>
                <button type='button' class='btn btn-success btn-lg' style='width:50%' @click="retry()">リトライ</button>
            </div>
            <div class='col-1'></div>
        </div><!--リトライ-->
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
                const stats = ref('none')//none:初期値,sending,stop,success,error
                var errfilelist =""

                const error = {
                    'UPLOAD_ERR_INI_SIZE' : 'upload_max_filesize ディレクティブの値を超えている',
                    'UPLOAD_ERR_FORM_SIZE' : '指定されたMAX_FILE_SIZEを超えている',
                    'UPLOAD_ERR_PARTIAL' : 'サーバーにチャンクの一部のみしかアップロードされていない',
                    'UPLOAD_ERR_NO_FILE' : 'アップロードされなかった',
                    'UPLOAD_ERR_NO_TMP_DIR' : 'サーバーにテンポラリフォルダがない' ,
                    'UPLOAD_ERR_CANT_WRITE'  : 'サーバーに書き込みに失敗',
                    'UPLOAD_ERR_UNKNOWN'  : '不明なエラー',
                    'FAILED TO SAVE FILE'  : 'サーバーにてファイル結合失敗',
                    'Request Entity Too Large' : 'Webサーバー(Nginx)のアップロード制限を超えた'
                };
                
                const errorString=m=>{// サーバー受け取ったエラーメッセージを変換
                    let keys = Object.keys(error);
                    let len = keys.length;
                    for(let i = 0; i < len ; i++){
                        if(m.indexOf(keys[i]) !== -1) return error[keys[i]];
                    }
                    return m;
                };

                var flow = new Flow({
                    target:'upload.php',
                    forceChunkSize:true,
                    chunkSize:1*1028*1028*150, //チャンクサイズ（小分けにするサイズです）
                });
                var flowfile= flow.files;
                var index=0
                var retry_index=[]
                var rindex=0

                const result = computed(()=>{
                    switch(stats.value){
                        case 'none':return ''
                        case 'sending':return '送信中'
                        case 'stop':return '停止'
                        case 'success':return '完了'
                        case 'error':return 'エラーあり'
                    }
                })

                const selectfiles =(e)=>{//動画選択
                    console_log('selectfiles')
                    if(flow.isUploading()){
                        alert("ファイル送信中です。");
                    }
                    let elem = document.getElementById(e.target.id)
                    flow.assignBrowse(elem,null,null,{'accept':'video/*'})
                    document.getElementById("progressbar").innerHTML = '0%'
                    document.getElementById("progressbar").style.width = '0%'
                    document.getElementById("progressbar").style.backgroundColor="#fff"
                    document.getElementById("progressbar").style.color="#FFA400"
                }
                const uploading = () =>{//アップロード実行
                    console_log(flow.files.length)
                    if(flowfile.length===0){
                        alert("ファイルが指定されてません。");
                    }else if(flow.isUploading()){
                        alert("ファイル送信中です。");
                    }else{
                        errfilelist = ""
                        console_log("アップロード実行")
                        index=0
                        flow.upload();
                        //stats.value = 'sending'
                        //IDD_Write_ForClassOBJ('LocalParameters',{'id':flow})
                    }
                }
                const resume = () =>{//アップロード再開
                    console_log("アップロード再開")
                    if(flow.isUploading()){
                        alert("ファイル送信中です。");
                    }else{
                        alert("おっと、寝てたわ。");
                        flow.resume();
                    }
                }
                const cancel = () =>{
                    console_log("アップロードキャンセル")
                    alert("アップロードキャンセル");
                    flow.cancel();
                    document.getElementById("progressbar").innerHTML = '0%'
                    document.getElementById("progressbar").style.width = '0%'
                }
                const pause = () =>{
                    alert("おやすみ。");
                    flow.pause()
                }
                const retry = () =>{
                    //alert("おやすみ。");
                    rindex=0
                    flow.retry()
                }
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
                
                flow.on('fileAdded', function(file, event){
                    index = index + Number(1)
                    document.getElementById('filelist').innerHTML += '<div class="col-1"></div><div class="col-8">' + file.name + '</div><div class="col-2" id="' + index + '">0%</div><div class="col-1"></div>'
                });
                flow.on('filesSubmitted', function(file) {
                    if(file.length===0){
                        alert("ファイルが指定されてません。");
                    }else{
                        console_log("アップロード準備OK")
                    }
                    
                });
                flow.on('progress',function(){
                    //プログレスバーの実行
                    //flow.progress() で進捗が取得できるのでそれを利用してプログレスバーを設定
                    if(flow.isUploading()){
                        stats.value = 'sending'                        
                    }else{
                        stats.value = 'stop'
                    }
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
                    flow.removeFile(file)
                });
                flow.on('fileRemoved',(file)=>{
                    console_log(`${file.name} リムーブ確認`);
                    //console_log(file);
                })
                flow.on('fileError', (file, message)=>{
                    // アップロード失敗したときの処理
                    console_log(`${file.name} アップロード失敗`);//今回はメッセージを表示します。
                    index = index + Number(1)
                    rindex = rindex + Number(1)
                    errfilelist += `<div class="col-1"></div><div class="col-8">${file.name}</div><div class="col-2" id="' + index + '">失敗:${errorString(message)}</div><div class="col-1"></div>`
                    document.getElementById(index).innerHTML = `失敗:${errorString(message)}`
                    retry_index[rindex]=index
                });
                flow.on('complete',(file)=>{
                    console_log("アップロードおしまい")
                    if(errfilelist===""){
                        flow.cancel()
                        stats.value = 'success'
                    }else{
                        stats.value = 'error'
                    }
                    
                    index=0
                    document.getElementById('filelist').innerHTML = errfilelist
                    get_files()
                    //stats.value=""
                    
                })
                flow.on('fileRetry',(file)=>{
                    rindex = rindex + Number(1)
                    document.getElementById(retry_index[rindex]).innerHTML = `retry`
                    console_log(`リトライしてます${file.name}`)
                })

                onMounted(() => {
                    get_files()
                });

                
                return {
                    files,
                    uploading,
                    selectfiles,
                    get_files,
                    stats,
                    retry,
                    cancel,
                    resume,
                    pause,
                    result,
                };
            }
        }).mount('#getlist');
    </script><!--vue-->


</BODY>
</html>









