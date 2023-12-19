<?php
require "php_header.php";
file_put_contents("error_log",date('Y/m/d-H:i:s')."：uploading index.php...\n",FILE_APPEND);
var_dump(getFileList("./upload/demo/"));
?>
<!DOCTYPE html>
<html lang='ja'>

<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head_bs5.php" 
    ?>
    <script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
    <TITLE>Video Uploader</TITLE>
</head>
<BODY id = 'body' style='background:black;' >
    <HEADER class='container' style='color:#FFA400'>
        <h1>Video Uploader</h1>
    </HEADER>
    <MAIN class='container' style='color:#fff;'>
        <div  class='row' style='margin-bottom:5px;' >
            <div class='col-12'>
                <label for='formFile' class='form-label' >アップロードする動画を選択（複数可）</label>
                <input class='form-control' required accept='video/*' type='file' id='formFile' 
                onchange='OnFileSelect(this);' type='file' multiple value='動画'>
               
                <ul id='ID001'></ul>
                
                
                <button class='btn btn-primary' onclick="up_submit()">送 信</button>
            </div>
        </div>

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
                    .get('ajax_loader.php?name=demo&iCount=10&first=' + first)
                    .then((response) => (files.value = [...files.value,...response.data],
                                        console.log('setup succsess')
                                        //,console.log(files))
                                        ))
                    .catch((error) => console.log(error));
                }
                
                const clear = () => {
                    files.value = ([])
                    axios
                        .get('ajax_loader.php?name=demo')
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
        
        function up_submit(){
            console.log('submit go!');
            const body = new FormData()
            //const body = document.getElementById('f_upload')

            // Read selected files
            const totalfiles = document.getElementById('formFile').files.length;
            for (var index = 0; index < totalfiles; index++) {
                body.append("upload_file[]", document.getElementById('formFile').files[index]);
            }
            console.log(...body.entries());

            
            axios.post("uploader.php", body)
            .then((response) => {
                alert('succsess')
                })
            .catch((error) => {
                alert('error')
                console.log(error)
                });
        }
          
        function OnFileSelect( inputElement ){
            const fileList = inputElement.files;  // ファイルリストを取得
            let fileCount = fileList.length;    // ファイルの数を取得
        
            // HTML文字列の生成
            let fileListBody = "選択されたファイルの数 = " + fileCount + "<br/><br/>";
        
            // 選択されたファイルの数だけ処理する
            for ( let i = 0; i < fileCount; i++ ) {
                let file = fileList[ i ];   // ファイルを取得
        
                // ファイルの情報を文字列に格納
                fileListBody += "<li>[ " + ( i + 1 ) + "ファイル目 ]";
                fileListBody += "name             = " + file.name + "/";
                fileListBody += "type             = " + file.type + "/";
                fileListBody += "size             = " + file.size + "/";
                fileListBody += "lastModifiedDate = " + file.lastModifiedDate + "/";
                fileListBody += "lastModified     = " + file.lastModified + "/";
                fileListBody += "</li>";
            }
        
            // 結果のHTMLを流し込む
            document.getElementById( "ID001" ).innerHTML = fileListBody;
        }
    </script>


</BODY>
</html>









