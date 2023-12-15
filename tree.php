<?php
  require "php_header.php";
  file_put_contents("error_log",date('Y/m/d-H:i:s')."：uploading index.php...\n",FILE_APPEND);

  $sql = "select replace(level,'0','') as lv ,name from levels where uid = :id order by level";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("id", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $dataset_json = json_encode($dataset, JSON_UNESCAPED_UNICODE);
//var_dump(getFileList("./upload/ryota/"));
?>
<!DOCTYPE html>
<html lang='ja'>

<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head_bs5.php" 
    ?>
    <TITLE>Video Uploader</TITLE>
</head>
<body>
  <div id='tree'>
    <ul>
      <template v-for='(list,index) in tree_obj' :key='list.lv'>
        <template v-if='list.lv.length ===1'><li>{{list.name}}</li></template>  
        <template v-if='list.lv.length ===2'><ul><li>{{list.name}}</li></ul></template>  
        <template v-if='list.lv.length ===3'><ul><ul><li>{{list.name}}</li></ul></ul></template>  
      </template>
    </ul>
  </div>
  <script>
    const { createApp, ref, onMounted, reactive } = Vue;
    createApp({
            setup() {
              const tree_obj = ref(<?php echo $dataset_json;?>)
              onMounted(() => {
                console_log('onMounted')
                console_log(tree_obj)
              })
              return {
                tree_obj,
              }
            }
        }).mount('#tree');

  </script>
</body>
