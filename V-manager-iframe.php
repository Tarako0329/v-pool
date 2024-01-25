<?php
    require "php_header.php";
   $lv = !empty($_GET["lv"])?$_GET["lv"]:"%";
   $token = csrf_create();
?>
<!DOCTYPE html>
<html lang='ja' style='overflow-x: hidden;'>
<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head_bs5.php" 
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<BODY id = 'body' style='background:black;' >
    <div id='getlist'>
    <HEADER style='height: 35px;padding:0;'>
        <div id='folderopen' @click='foldersetOpen("","","disp")' role="button" data-bs-toggle='modal' data-bs-target='#folderediter'>フォルダ選択・作成 <i class="bi bi-folder2-open h3 treei"></i></div>
        <transition>
            <div v-if="msg!==''" class="alert alert-warning" role="alert">
                {{msg}}
            </div>
        </transition>
    </HEADER>
    <MAIN class='container' style='color:#fff;padding:35px 0 30px 0;' >
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
        <!--フォルダツリー-->
        <!--
        <div v-show='foldertreedisp' id='foldertree'>
            <div class='text-end' id='foldertree_close' role='button' @click='foldersetClose()'>✖</div>
            <div style='padding:0;'>
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
        </div>フォルダツリー-->
        
        <div class='row' style='height: 40px;padding:0;'>
            <div class='col-4 text-center fbtn'><a class='a_none' href="#top0" @click='move_page(-6)'>＜＜</a></div>
            <div class='col-4' style='border-left:solid 1px #FFA400;border-right:solid 1px #FFA400;margin:0;'>
            </div>
            <div class='col-4 text-center fbtn'><a class='a_none' href="#top0" @click='move_page(6)'>＞＞</a></div>
        </div>
    </MAIN>
    <!--
    <FOOTER>
        <div class='row' style='height:0%;padding:0;'>
            <div class='col-4 text-center fbtn'><a class='a_none' href="#top0" @click='move_page(-6)'>＜＜</a></div>
            <div class='col-4' style='border-left:solid 1px #FFA400;border-right:solid 1px #FFA400;margin:0;'>
            </div>
            <div class='col-4 text-center fbtn'><a class='a_none' href="#top0" @click='move_page(6)'>＞＞</a></div>
        </div>
    </FOOTER>
    -->
	<div class='modal fade' id='folderediter' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
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
    
    <script>//vue.js
        const { createApp, ref, onMounted, reactive,computed,watch } = Vue;
        createApp({
            setup() {
                //動画一覧関連↓
                const files = ref([])
                var joken = {'lv':'<?php echo $lv;?>'}  //動画一覧の表示条件
                var token = '<?php echo $token;?>'
                const msg = ref('')
                const get_files = () => {//アップロード後の分類等未設定の動画一覧を取得
                    //console_log(joken.lv)
                    axios
                    .get(`ajax_get_files.php?lv=${joken.lv}`)
                    .then((response) => {
                        files.value = [...response.data],
                        console_log('get_files succsess')
                        //console_log(files.value)
                    })
                    .catch((error) => console.log(error));
                }
                const filetrash = (no,name) =>{//動画削除
                    if(confirm('本当に削除しますか？(復元はできません)') === false){
                        return
                    }
                    axios
                    .get(`ajax_del_file.php?F=${name}&FN=${no}&vp_csrf_token=${token}`)
                    .then((response) => {
                        console_log(response)
                        if(response.data.status==="success"){
                            msg.value="ファイルを削除しました。"
                            token = response.data.token
                            get_files()
                            console_log('filetrash succsess')
                        }else{
                            alert(response.data)
                            console_log('filetrash 失敗')
                        }
                    })
                    .catch((error) => console.log(error));
                }

                const iv = ref(0)
                const fileview = computed(()=>{
                    let newlist = files.value.slice(Number(iv.value),Number(iv.value) + 6)
                    return newlist
                })

                const move_page = (i) =>{
                    console_log('viewer')
                    if(iv.value + Number(i)<0){
                        iv.value = 0
                    }else if(iv.value + Number(i)>files.value.length){
                        //iv.value = 0
                    }else{
                        iv.value = iv.value + Number(i)
                    }
                    
                    console_log(iv)
                }
                //動画一覧関連↑

                //フォルダツリー関連↓
                const foldertreedisp = ref(false)   //フォルダエリアの表示非表示
                const folderAreaRole = ref('')      //フォルダエリアの役割切換(mng:動画をフォルダに入れる or disp:フォルダ内の動画を表示)
                const tree = ref()                  //フォルダツリーのデータ配列
                const get_tree = () => {//フォルダツリーのデータを取得
                    axios
                    .get('ajax_get_tree.php')
                    .then((response) => {
                        tree.value = [...response.data]
                        //console.log(tree.value)
                        console_log('ajax_get_tree succsess')
                        //console_log(files.value)
                    })
                    .catch((error) => console.log(error));
                }
                var Findex,FfileNo  //編集対象
                const foldersetOpen = (index,fileNo,role) =>{//フォルダ選択エリアを表示
                    console_log(role)
                    //foldertreedisp.value = true
                    if(role==='mng'){
                        Findex = index
                        FfileNo = fileNo
                        console_log(fileNo)
                    }else if(role==='disp'){
                    }else{
                        return
                    }
                    folderAreaRole.value = role
                }
                const foldersetClose = () =>{//フォルダ選択を反映し、閉じる
                    console_log("foldersetClose")
                    if(folderAreaRole.value==="mng"){
                        axios
                        .get(`ajax_upd_filefolder.php?lv=${files.value[Findex]['level']}&fileNo=${FfileNo}&vp_csrf_token=${token}`)
                        .then((response) => {
                            console_log(response)
                            if(response.data.status==="success"){
                                if(before_choese_i!==undefined){
                                    before_choese_i.className = "bi bi-folder h3 treei"
                                    before_choese_li.className = "treeil"
                                }
                                token = response.data.token
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

                        foldertreedisp.value = false    //ウィンドを閉じる
                        Findex = null   //編集ファイルリストのインデックスをクリア
                        FfileNo = null  //編集ファイルのファイルNOをクリア

                    }else if(folderAreaRole.value==="disp"){
                        foldertreedisp.value = false
                        if(before_choese_i!==undefined){
                            before_choese_i.className = "bi bi-folder h3 treei"
                            before_choese_li.className = "treeil"
                        }
                    }else{
                        return
                    }
                    tree_tenkai_list.value = []
                }

                var before_choese_i
                var before_choese_li
                const tree_tenkai_list = ref([])
                const choese_folder = (index) =>{//フォルダを選択する
                    console_log(`choese [${tree.value[index]["level"]}:${tree.value[index]["name"]}]`)

                    if(before_choese_i!==undefined){
                        before_choese_i.className = "bi bi-folder h3 treei"
                        before_choese_li.className = "treeil"
                    }
                    before_choese_i = document.getElementById("i_"+tree.value[index]["level"])
                    before_choese_li = document.getElementById("li_"+tree.value[index]["level"])
                    document.getElementById("i_"+tree.value[index]["level"]).className = "bi bi-folder-check h3 treei"
                    document.getElementById("li_"+tree.value[index]["level"]).className = "treeil fw-bold tree_choese"

                    if(folderAreaRole.value === 'mng'){

                        files.value[Findex]["level"] = tree.value[index]["level"]
                        files.value[Findex]["fullLvName"] = tree.value[index]["fullLvName"]

                    }else if(folderAreaRole.value === 'disp'){
                        open_folder(tree.value[index]["lv"])
                    }

                    
                    if(tree_tenkai_list.value.indexOf(tree.value[index]["level"])>=0){
                        let target = tree_tenkai_list.value[tree_tenkai_list.value.indexOf(tree.value[index]["level"])]
                        target = target.substr(0,tree.value[index]["kaisou"])
                        console_log(`あるよ！${target}`)
                        tree_tenkai_list.value = tree_tenkai_list.value.filter(row => !(row.startsWith(target) ))
                    }else{
                        tree_tenkai_list.value.push(tree.value[index]["level"])
                    }
                    console_log(tree_tenkai_list.value)
                }

                const open_folder = (lv) =>{
                    joken = {'lv':`${lv}%`}
                    console_log(joken)
                    get_files()
                    //foldertreedisp.value=false
                }
                //フォルダツリー関連↑

                //フォルダ構成の編集メソッド
                const foldernameset = (index) =>{//新規フォルダの名称入力ON
                    tree.value[index]["newfolder"] = "display"
                }
                const ins_tree = (index) =>{//新規フォルダの作成
                    let uplevel = tree.value[index]["lv"]
                    let name = tree.value[index]["newname"]
                    console_log(`${uplevel}:${name}`)
                    if(name.length===0){
                        alert("フォルダ名を入力してください")
                        return
                    }
                    axios
                    .get(`ajax_ins_tree.php?lv=${uplevel}&name=${name}&vp_csrf_token=${token}`)
                    .then((response) => {
                        console_log(response)
                        if(response.data.status==="success"){
                            token = response.data.token
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

                //title編集関連
                const title_cg = ref('')
                const title_change = (taishou) =>{
                    title_cg.value = taishou
                }
                const title_write = (index) =>{
                    console_log(`title_write`)
                    let newtitle = document.getElementById(`list[${index}][titel]`).value
                    console_log(`${newtitle}`)

                    axios
                    .get(`ajax_upd_filetitle.php?title=${newtitle}&fileNo=${files.value[index]['fileNo']}&vp_csrf_token=${token}`)
                    .then((response) => {
                        console_log(response)
                        if(response.data.status==="success"){
                            files.value[index]["title"] = newtitle
                            title_cg.value = ''
                            token = response.data.token
                            console_log('ajax_upd_filetitle succsess')
                        }else{
                            alert(response.data)
                            console_log('ajax_upd_filetitle 失敗')
                        }
                    })
                    .catch((error,response) => {
                        console_log(error)
                        console_log(response)
                    });
                }

                watch(msg,()=>{
                    console_log('watch msg => '+msg.value)
                    setTimeout(()=>{msg.value=""}, 3000);//1.5s
                    //setTimeout(setframeheight, 1000);//0.5s
                })

                onMounted(() => {
                    get_files()
                    get_tree()
                });

                
                return {
                    files,
                    fileview,
                    move_page,
                    iv,
                    get_files,
                    filetrash,
                    foldertreedisp,
                    folderAreaRole,
                    foldersetOpen,
                    foldersetClose,
                    tree,
                    tree_tenkai_list,
                    foldernameset,
                    ins_tree,
                    choese_folder,
                    open_folder,
                    msg,
                    title_cg,
                    title_change,
                    title_write,
                    //next_load,
                };
            }
        }).mount('#getlist');
    </script><!--vue-->


</BODY>
</html>