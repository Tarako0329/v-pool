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
    <HEADER class='text-center' style='color:#FFA400' id='header'>
        <h1><a href='/' class='a_none'>Video Uploader</a></h1>
        <div class='youkoso'><?php echo "ようこそ".$_SESSION["name"]."さん";?></div>
    </HEADER>
    <MAIN class='container' style='color:#fff;padding-bottom:0;' id='getlist'>
        <div id='uploadarea'>
            <transition>
            <div v-if="msg!==''" class="alert alert-warning" role="alert">
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
            <div v-if='["fileset","stop"].includes(stats)' class='row'><!--送信ボタン-->
                <div class='col-1'></div>
                <div class='col-10'>
                    <button type='button' class='btn btn-primary btn-lg' style='width:50%' @click="uploading()">送 信</button>
                    <button type='button' class='btn btn-warning btn-lg' style='width:50%' @click="cancel()">キャンセル</button>
                </div>
                <div class='col-1'></div>
            </div><!--送信ボタン-->
            <div v-if='["sending","stop"].includes(stats)' class='row fadein'><!--再開・停止ボタン-->
                <div class='col-1'></div>
                <div class='col-10'>
                    <button type='button' class='btn btn-warning btn-lg' style='width:50%' @click="cancel()">キャンセル</button>
                    <button type='button' class='btn btn-success btn-lg' style='width:50%' @click="pause()">中断</button>
                    <button type='button' class='btn btn-success btn-lg' style='width:50%' @click="resume()">再開</button>
                </div>
                <div class='col-1'></div>
            </div><!--再開・停止ボタン-->
            <div v-if='["error"].includes(stats)' class='row fadein'><!--リトライ-->
                <div class='col-1'></div>
                <div class='col-10'>
                    <button type='button' class='btn btn-success btn-lg' style='width:50%' @click="retry()">リトライ</button>
                </div>
                <div class='col-1'></div>
            </div><!--リトライ-->
            <div v-if='["sending","stop"].includes(stats)' class='row' style='margin-bottom:15px;'><!--progressbar-->
                <div class='col-3 text-end'>{{result}}</div>
                    <div class='col-8' style='padding:2px 12px 2px 2px;'>
                        <div style='border:solid 1px #FFA400;height:100%;width:100%;'>
                            <div class='text-center' style='height:100%;width:0%;padding:0;margin:0;background-color:#fff;color:#FFA400;' id='progressbar'>0%</div>
                        </div>
                    </div>
                <div class='col-1'></div>
            </div><!--progressbar-->
            <div class='row fadein' style='border:solid 1px #FFA400;margin-bottom:15px;'><!--ここにファイルリスト表示-->
                <template v-for='(file,index) in filelist' :key='file.name'>
                    <div class="col-1"></div><div class="col-8">{{file.name}}</div><div class="col-2" :id="index">{{file.persent}}</div><div class="col-1"></div>
                </template>
                <!--{{filelist}}-->
            </div><!--ここにファイルリスト表示-->
            <hr>
            <div class='row text-center'><h3>未分類動画一覧</h3></div>
        </div>

        <div style='width:100%;height:50%;' id='Vmanager'>
            <iframe src="V-manager-iframe.php" width="100%" height="100%" id='Vmanager-frame'></iframe>
        </div>

    </MAIN>
    <script>//vue.js
        const { createApp, ref, onMounted, reactive,computed,watch } = Vue;
        createApp({
            setup() {
                const stats = ref('none')//none:初期値,fileset,sending,stop,success,error,cancel
                const filelist = ref([])//upload files
                const msg = ref('') //alert msg

                var errfilelist =""
                const setfilelist = (i,name,persent) =>{
                    filelist.value[i]={'name':name,'persent':persent}
                }

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
                        msg.value = "ファイル送信中です。";
                    }
                    filelist.value = []
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
                        msg.value = "ファイル送信中です。";
                    }else{
                        errfilelist = ""
                        console_log("アップロード実行")
                        index=0
                        flow.upload();
                    }
                }
                const resume = () =>{//アップロード再開
                    console_log("アップロード再開")
                    if(flow.isUploading()){
                        msg.value = "ファイル送信中です。";
                    }else{
                        msg.value = "再開します。";
                        flow.resume();
                    }
                }
                const cancel = () =>{
                    if(flowfile.length===0){
                        return
                    }
                    console_log("アップロードキャンセル")
                    //alert("アップロードキャンセル");
                    msg.value = "キャンセルしました";
                    index = 0
                    rindex=0
                    flow.cancel();
                    document.getElementById("progressbar").innerHTML = '0%'
                    document.getElementById("progressbar").style.width = '0%'
                    filelist.value = []
                    stats.value='cancel'
                }
                const pause = () =>{
                    msg.value = "おやすみ。";
                    flow.pause()
                }
                const retry = () =>{
                    //alert("おやすみ。");
                    rindex=0
                    flow.retry()
                }
                flow.on('fileAdded', function(file, event){
                    console_log(file.name)
                    filelist.value[index] = {'name':file.name,'persent':'0%'}
                    index = index + Number(1)
                });
                flow.on('filesSubmitted', function(file) {
                    if(file.length===0){
                        alert("ファイルが指定されてません。");
                    }else{
                        console_log("アップロード準備OK")
                    }
                    //setframeheight()
                    stats.value='fileset'
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
                    //document.getElementById(index).innerHTML = '100%'
                    filelist.value[index]['persent'] = '100%'
                    index = index + Number(1)
                    flow.removeFile(file)
                });
                flow.on('fileRemoved',(file)=>{
                    console_log(`${file.name} リムーブ確認`);
                    //console_log(file);
                })
                flow.on('fileError', (file, message)=>{
                    // アップロード失敗したときの処理
                    console_log(`${file.name} アップロード失敗`);//今回はメッセージを表示します。
                    filelist.value[index]['persent'] = `失敗:${errorString(message)}`
                    index = index + Number(1)
                    rindex = rindex + Number(1)
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
                    //document.getElementById('filelist').innerHTML = errfilelist
                    document.getElementById("Vmanager-frame").contentWindow.location.reload();
                    
                })
                flow.on('fileRetry',(file)=>{
                    rindex = rindex + Number(1)
                    document.getElementById(retry_index[rindex]).innerHTML = `retry`
                    console_log(`リトライしてます${file.name}`)
                })

                const setframeheight = () =>{
                    let Vmanager = document.getElementById('Vmanager')
                    console_log('start setframeheight ' + Vmanager.style.height)

                    let up = document.getElementById('uploadarea').scrollHeight
                    let head = document.getElementById('header').scrollHeight
                    let w_ih = window.innerHeight;
                    
                    Vmanager.style.height = `${Number(w_ih) - Number(up) - Number(head)}px`
                    console_log('end setframeheight ' + Vmanager.style.height)
                }
                watch(stats,()=>{
                    console_log('watch stats => '+stats.value)
                    setTimeout(setframeheight, 1000);//0.5s
                })
                watch(msg,()=>{
                    console_log('watch msg => '+stats.value)
                    setTimeout(()=>{msg.value=""}, 3000);//1.5s
                    setTimeout(setframeheight, 1000);//0.5s
                })

                onMounted(() => {
                    setframeheight()
                });

                
                return {
                    //files,
                    uploading,
                    selectfiles,
                    filelist,
                    stats,
                    retry,
                    cancel,
                    resume,
                    pause,
                    result,
                    msg,
                };
            }
        }).mount('#getlist');
    </script><!--vue-->


</BODY>
</html>









