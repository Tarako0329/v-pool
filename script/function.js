//js 共通関数格納予定
const console_log=(log)=>{
  //lv:all=全環境 undefined=本番以外
  //console.log(lv)
  var uri = new URL(window.location.href);
  //console.log(uri.hostname)
  if(uri.hostname==="localhost"){
    console.log(log)
  }
}

