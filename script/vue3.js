const { createApp, ref, onMounted, onBeforeMount, computed,watch } = Vue;
const V_manager = (Where_to_use,p_lv,p_token) => createApp({
    setup() {
        //動画一覧関連↓
        const files = ref([])
        //var joken = {'lv':'<?php echo $lv;?>'}  //動画一覧の表示条件
        //var token = '<?php echo $token;?>'
        var joken = {'lv':p_lv}  //動画一覧の表示条件
        var token = p_token
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
})