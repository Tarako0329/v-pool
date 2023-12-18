<?php
require "php_header.php";
$sql = "insert into filelist(uid,filename,before_name) values(:id,:filename,:before_name)";
$stmt = $pdo_h->prepare($sql);
$stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
$stmt->bindValue("filename", "test", PDO::PARAM_STR);
$stmt->bindValue("before_name", "test", PDO::PARAM_STR);
$stmt->execute();

//var_dump(getFileList("./upload/ryota/"));
?>
<!DOCTYPE html>
<html lang='ja'>

<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head_bs5.php" 
    ?>
    <script src="./script/flow.js-master/src/flow.js"></script>
    <TITLE>Video Uploader</TITLE>
</head>
<BODY id = 'body' style='background:black;' >
    <HEADER class='container' style='color:#FFA400'>
        <h1>Video Uploader</h1>
    </HEADER>
    <MAIN class='container' style='color:#fff;'>
        <div class='row' style='margin-bottom:15px;' >
            <div class='col-1'></div>
            <div class='col-10'>
                <label for='formFile' class='form-label' >アップロードする動画を選択（複数可）</label>
                <button class='btn btn-primary' style='width:100%' id='formFile' >動画選択</button>
            </div>
            <div class='col-1'></div>
        </div>
        <div class='row' id='filelist' style='margin-bottom:15px;'>
        </div>
        <div class='row' style='margin-bottom:15px;'>
            <div class='col-1'></div>
                <div class='col-10' style='border:solid 1px #FFA400;padding:2px;'>
                    <div class='text-center' style='height:100%;width:0%;padding:0;margin:0;background-color:#fff;color:#FFA400;' id='progressbar'></div>
                </div>
                <div class='col-1'></div>
            </div>
        <div class='row'>
            <div class='col-1'></div>
            <div class='col-10'>
                <button class='btn btn-primary' style='width:100%' onclick="uploading()">送 信</button>
            <div class='col-1'></div>
        </div>
            </div>
        </div>
        <!--
        <div class='row' id='getlist'>
            <div><button @click="clear" class='btn btn-primary' style='width:80px;'>{{message}}</button></div>
            
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
        -->
    </MAIN>
    <FOOTER>
        
    </FOOTER>
    <script>
        const { createApp, ref, onMounted, reactive } = Vue;
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

                const updated_users = Vue.computed(() => {
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
                    .get('ajax_loader.php?name=eriko&iCount=10&first=' + first)
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
        var flow = new Flow({
            target:'upload.php',
            chunkSize:1*1028*1028*10, //チャンクサイズ（小分けにするサイズです）
        });
        var flowfile= flow.files;
        var index=0
        flow.assignBrowse(document.getElementById('formFile'));

        flow.on('fileAdded', function(file, event){
            //console.log(file, event);
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
            //flow.upload();
        });
        flow.on('progress',function(){
            //プログレスバーの実行
            //flow.progress() で進捗が取得できるのでそれを利用してプログレスバーを設定
            document.getElementById("progressbar").innerHTML = Math.floor(flow.progress()*100) + '%'
            document.getElementById("progressbar").style.width = Math.floor(flow.progress()*100) + '%'
        });
        flow.on('complete',()=>{
            console_log("アップロードおしまい")
            flow.off()
            index=0

        })
        const uploading = () =>{
            flow.upload();
            index=0
            console_log("アップロード実行")
        }
    </script>


</BODY>
</html>









