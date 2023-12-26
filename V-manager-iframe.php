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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<BODY id = 'body' style='background:black;' >
    <form action='update.php' method="post" id='getlist'>
    <MAIN class='container' style='color:#fff;padding:0;' >
        <hr>
        <div id='mibunrui'><!--動画一覧-->
            
            <div class='row'>
            <template v-for='(file,index) in files' :key='file.fileNo'>
                <div class ='col-4 col-lg-2' style='margin-bottom:20px;'>
			        <video style='max-width:100%;width:100%;'preload='metadata' controls muted :src='`./upload/${file.uid}/${file.filename}#t=0.01`'></video>
	    		</div>
                <div class ='col-8 col-lg-4' style='margin-bottom:20px;' :id='`File_NO_${index}`'>
                    <!--未実装<div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" :id="index" :name='`list[${index}][upd]`'>
                        <label class="form-check-label" :for="index">一括更新対象</label>
                    </div>-->
                    <p style='color:#fff;margin-bottom: 4px;'>ファイル名：{{file.before_name}}</p>
                    <p style='color:#fff;margin-bottom: 4px;'>保存日時：{{file.insdate}}</p>
                    <label class="form-check-label" :for='`list[${index}][titel]`' style='color:#fff;'>タイトル：</label>
                    <input type='text' class="form-control" :value=file.titel :name='`list[${index}][titel]`' :id='`list[${index}][titel]`'>
                    <label class="form-check-label" :for='`list[${index}][name]`' style='color:#fff;'>フォルダ：{{file.fullLvName}}</label>
                    <button type='button' class='btn btn-outline-light ib' @click='foldersetOpen(index,file.fileNo)'><i class="bi bi-folder-plus h1"></i></button>

                    <!--<input type='hidden' class="form-control" :value=file.name :name='`list[${index}][name]`' :id='`list[${index}][name]`' placeholder="例：2020年/5月/運動会">-->
                    <!--未実装<label class="form-check-label" :for='`list[${index}][tags]`' style='color:#fff;'>タグ：</label>
                    <i class="bi bi-hash h1"></i>
                    <input type='text' class="form-control" :value=file.tags :name='`list[${index}][tags]`' :id='`list[${index}][tags]`' placeholder="例：#子供#運動会">-->
                    <input type='hidden' :value="file.fileNo" :name='`list[${index}][fileNo]`'>
                </div>
            </template>
            </div>
        </div><!--動画一覧-->
        <div v-show='foldertreedisp' id='foldertree'><!--フォルダツリー-->
            <div id='foldertree_close' role='button' @click='foldersetClose()'>閉じる</div>
            <ul style='padding:0;'>
                <template v-for='(list,index) in tree' :key='list.level'>
                    <li v-if='index===0' :style='{"padding-left":list.padding}' class='treeil'>
                        <i class="bi bi-folder-plus h3 treei" style='color:#FFA400;'></i>
                        <input class="form-control form-control-sm tree_input" type='text' placeholder="NEW フォルダ名" v-model='list.newname'>
                        <button type='button' class='btn btn-outline-light treeb' @click='ins_tree(index)'>作成</button>
                    </li>
                    <li :style='{"padding-left":list.padding}' class='treeil' :id='"li_"+list.level' @click='choese(index)' role='button'><i class="bi bi-folder h3 treei" :id='"i_"+list.level'></i>{{list.name}}</li>
                    <li v-if='index!==0' :style='{"padding-left":list.next_padding}' class='treeil'>
                        <template v-if='list.newfolder==="none"' >
                            <a href="#" style='color:#FFA400;' @click='foldernameset(index)'><i class="bi bi-folder-plus h3 treei"></i>新規作成</a>
                        </template>
                        <template v-if='list.newfolder==="display"' >
                            <i class="bi bi-folder-plus h3 treei" style='color:#FFA400;'></i>
                            <input class="form-control form-control-sm tree_input" type='text' placeholder="NEW フォルダ名" v-model='list.newname'>
                            <button type='button' class='btn btn-outline-light treeb' @click='ins_tree(index)'>作成</button>
                        </template>
                    </li>
                </template>
            </ul>
        </div><!--フォルダツリー-->
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

                const foldertreedisp = ref(false)
                const tree = ref()
                const get_tree = () => {//アップロード後の分類等未設定の動画一覧を取得
                    axios
                    .get('ajax_get_tree.php')
                    .then((response) => {
                        tree.value = [...response.data]
                        console.log(tree.value)
                        console_log('ajax_get_tree succsess')
                        //console_log(files.value)
                    })
                    .catch((error) => console.log(error));
                }
                var Findex,FfileNo  //編集対象
                const foldersetOpen = (index,fileNo) =>{//フォルダ選択エリアを表示
                    foldertreedisp.value = true
                    Findex = index
                    FfileNo = fileNo
                    console_log(fileNo)
                }
                const foldersetClose = () =>{//フォルダ選択を反映し、閉じる
                    axios
                    .get(`ajax_upd_filefolder.php?lv=${files.value[Findex]['level']}&fileNo=${FfileNo}`)
                    .then((response) => {
                        console_log(response)
                        if(response.data==="success"){
                            console_log('ajax_upd_filefolder succsess')
                        }else{
                            alert(response.data)
                            console_log('ajax_upd_filefolder 失敗')
                        }
                    })
                    .catch((error,response) => {
                        console_log(error)
                        console_log(response)
                    });


                    foldertreedisp.value = false
                    //console_log(index)
                    Findex = null
                    FfileNo = null
                }

                var before_choese_i
                var before_choese_li
                const choese = (index) =>{//フォルダを選択する
                    console_log(`choese [${tree.value[index]["level"]}:${tree.value[index]["name"]}]`)
                    if(before_choese_i!==undefined){
                        before_choese_i.className = "bi bi-folder h3 treei"
                        before_choese_li.className = "treeil"
                    }
                    before_choese_i = document.getElementById("i_"+tree.value[index]["level"])
                    before_choese_li = document.getElementById("li_"+tree.value[index]["level"])
                    
                    document.getElementById("i_"+tree.value[index]["level"]).className = "bi bi-folder-check h3 treei"
                    document.getElementById("li_"+tree.value[index]["level"]).className = "treeil fw-bold tree_choese"

                    files.value[Findex]["level"] = tree.value[index]["level"]
                    files.value[Findex]["name"] = tree.value[index]["name"]
                }

                //フォルダ構成の編集メソッド
                const foldernameset = (index) =>{//新規フォルダの名称入力ON
                    tree.value[index]["newfolder"] = "display"
                }
                const ins_tree = (index) =>{//新規フォルダの作成
                    let uplevel = tree.value[index]["lv"]
                    let name = tree.value[index]["newname"]
                    if(name.length===0){
                        alert("フォルダ名を入力してください")
                        return
                    }
                    axios
                    .get(`ajax_ins_tree.php?lv=${uplevel}&name=${name}`)
                    .then((response) => {
                        console_log(response)
                        if(response.data==="success"){
                            get_tree()
                            console_log('ajax_ins_tree succsess')
                        }else{
                            alert(response.data)
                            console_log('ajax_ins_tree 失敗')
                        }
                    })
                    .catch((error,response) => {
                        console_log(error)
                        console_log(response)
                    });

                }

                onMounted(() => {
                    get_files()
                    get_tree()
                });

                
                return {
                    files,
                    get_files,
                    foldertreedisp,
                    foldersetOpen,
                    foldersetClose,
                    tree,
                    foldernameset,
                    ins_tree,
                    choese,
                };
            }
        }).mount('#getlist');
    </script><!--vue-->


</BODY>
</html>









