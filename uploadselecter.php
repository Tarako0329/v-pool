<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script src="./script/flow.js-master/src/flow.js"></script>
  <title>Flow.jsのテスト</title>
<style>
/*プログレスバーのスタイル（仮）*/
.progress{
  width:100%;
  border:1px solid #000;
  box-sizing: border-box;
  margin:30px 0;
}
.bar{
  height:30px;
  width:0;
  background:#ccc;
}
</style>
</head>
<body>

  <button id="browseButton">upload</button>
  <div class="progress"><div class="bar"></div></div>
  <div class="img-box"></div>

<script>
var flow = new Flow({
  target:'upload.php',
  chunkSize:1*1028*1028*10, //チャンクサイズ（小分けにするサイズです）
});
var flowfile= flow.files;

flow.assignBrowse(document.getElementById('browseButton'));
flow.on('fileSuccess', function(file,message){
  // アップロード完了したときの処理
  alert("アップロード完了");//今回はメッセージを表示します。
});
flow.on('filesSubmitted', function(file) {
  // アップロード実行
  flow.upload();
});
flow.on('progress',function(){
  //プログレスバーの実行
  //flow.progress() で進捗が取得できるのでそれを利用してプログレスバーを設定
  $('.bar').css({width:Math.floor(flow.progress()*100) + '%'});
});
flow.on('fileSuccess',function(file){
  // アップロードが完了したときの処理
  // ....
});
</script>


  <button onclick="flow.resume(); return(false);">再開</a>
  <button onclick="flow.pause(); return(false);">停止</a>

</body>
</html>
