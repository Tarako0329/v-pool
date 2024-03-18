//js 共通関数格納予定
const console_log=(log)=>{
  //lv:all=全環境 undefined=本番以外
  //console.log(lv)
  var uri = new URL(window.location.href);
  //console.log(uri.hostname)
  if(uri.hostname==="localhost"){
    console.log(log)
  }else{
    console.log(log)
  }
}


const GET_TREE = ()=>{//フォルダツリー取得
	return new Promise((resolve, reject) => {
		GET_TREE_SHORI(resolve);
	});
}
const GET_TREE_SHORI = (resolve) =>{
  let obj
  axios
  .get(`ajax_get_tree.php`)
  .then((response) => {
    obj = response.data
    console_log('ajax_get_tree succsess')
  })
  .catch((error)=>{
    console_log('ajax_get_tree.php ERROR')
    console_log(error)
  })
  .finally(()=>{
    resolve(obj)
  })
}


/*サンプル
const GET_USER2 = ()=>{//サイト設定情報取得
	return new Promise((resolve, reject) => {
		GET_USER_SHORI(resolve);
	});
}
const GET_USER_SHORI = (resolve) =>{
  let obj
  axios
  .get(`ajax_get_usersMSonline.php`)
  .then((response) => {
    obj = response.data
    console_log('ajax_get_usersMSonline succsess')
  })
  .catch((error)=>{
    console_log('ajax_get_usersMSonline.php ERROR')
    console_log(error)
  })
  .finally(()=>{
    resolve(obj)
  })
}
*/