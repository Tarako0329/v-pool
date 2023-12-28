<?php
// =========================================================
// オリジナルログ出力(error_log)
// =========================================================
function log_writer($pgname,$msg){
    $log = print_r($msg,true);
    file_put_contents("error_log","[".date("Y/m/d H:i:s")."] ORG_LOG from [".$_SERVER["PHP_SELF"]." -> ".$pgname."] => ".$log."\n",FILE_APPEND);
}
// =========================================================
// オリジナルログ出力(error_log)
// =========================================================
function next_char($char){
    if($char>=0 and $char<9){
        return $char+1;
    }else if($char==9){
        return "A";
    }else if($char>="A" and $char<"Z"){
        return ++$char;
    }else if($char==="Z"){
        return "a";
    }else if($char>="a" and $char<"z"){
        return ++$char;
    }else{
        return "error";
    }
}


// =========================================================
// 数字を3桁カンマ区切りで返す(整数のみ対応)
// =========================================================
function return_num_disp($number) {
    //$return_number = "";
    //$zan_mojisu = 0;
    $return_number = null;
    if(preg_match('/[^0-9]/',$number)==0){//0～9以外が存在して無い場合、数値として処理
        $shori_moji_su = mb_strlen($number) - 3;
        $zan_mojisu = null;
        
        while($shori_moji_su > 0){
            $return_number = $return_number.",".mb_substr($number,$shori_moji_su,3);
            $zan_mojisu = $shori_moji_su;
            $shori_moji_su = $shori_moji_su - 3;
        }
        
        $return_number = mb_substr($number,0,$zan_mojisu).$return_number;
    }else{
        $return_number = $number;
    }
    return $return_number;
}
// =========================================================
// トークンを作成
// =========================================================
function get_token() {
    $TOKEN_LENGTH = 16;//16*2=32桁
    $bytes = openssl_random_pseudo_bytes($TOKEN_LENGTH);
    return bin2hex($bytes);
}
// =========================================================
// トークンの削除
// =========================================================
function delete_old_token($token, $pdo) {
    //プレースホルダで SQL 作成
    $sql = "DELETE FROM AUTO_LOGIN WHERE token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $token, PDO::PARAM_STR);
    $stmt->execute();
}


function csrf_create(){
    //INPUT HIDDEN で呼ぶ
    $token = get_token();
    $_SESSION['vp_csrf_token'] = $token;

	//自動ログインのトークンを１週間の有効期限でCookieにセット
    //setCookie("webrez_token", $token, time()+60*60*24*7, "/", null, TRUE, TRUE); // secure, httponly
    //setCookie("vp_csrf_token", $token, time()+60*60*24*2, "/", "", false, TRUE);
    setCookie("vp_csrf_token", $token, time()+60*60*24*2, "/", "",true,true);
    
    return $token;
}
// =========================================================
// データ更新時のセキュリティ対応（セッション・クッキー・ポストのチェック）
//　一元化 (リファイラ[xxx.php,xxx.php],[S:session,C:cookie,G:get,P:post])
// =========================================================
function csrf_checker($from,$chkpoint){
    //リファイラーチェック
    $chkflg=false;
    foreach($from as $row){
        if(false !== strpos($_SERVER['HTTP_REFERER'],ROOT_URL.$row)){
            $chkflg=true;
            log_writer("func:csrf_checker","HTTP_REFERER success \$_SERVER[".$_SERVER['HTTP_REFERER']."]");
            log_writer("func:csrf_checker","HTTP_REFERER success ParamUrl[".ROOT_URL.$row."]");
            break;
        }
    }
    if($chkflg===true){
        $i=0;
        $csrf="";
        $checked="";
        foreach($chkpoint as $row){
            if($row==="S"){
                $csrf_ck = (!empty($_SESSION["vp_csrf_token"])?$_SESSION["vp_csrf_token"]:"\$_SESSION empty");
                $checked=$checked."S";
                unset($_SESSION['vp_csrf_token']) ; // セッション側のトークンを削除し再利用を防止
            }else if($row==="C"){
                $csrf_ck = (!empty($_COOKIE["vp_csrf_token"])?$_COOKIE["vp_csrf_token"]:"\$_COOKIE empty");
                $checked=$checked."C";
                setCookie("vp_csrf_token", '', -1, "/", "", TRUE, TRUE); // secure, httponly// クッキー側のトークンを削除し再利用を防止
            }if($row==="G"){
                $csrf_ck = (!empty($_GET["vp_csrf_token"])?$_GET["vp_csrf_token"]:"\$_GET empty");
                $checked=$checked."G";
            }if($row==="P"){
                $csrf_ck = (!empty($_POST["vp_csrf_token"])?$_POST["vp_csrf_token"]:"\$_POST empty");
                $checked=$checked."P";
            }
            if($i!==0){
                if($csrf !== $csrf_ck){
                    $chkflg=false;
                    log_writer("func:csrf_checker","CSRF failed [".$checked."]");
                    log_writer("func:csrf_checker","CSRF failed [".$csrf."]");
                    log_writer("func:csrf_checker","CSRF failed [".$csrf_ck."]");
                    $chkflg = "セッションが正しくありません";
                    break;
                }else{
                    log_writer("func:csrf_checker","CSRF success [".$checked."]");
                    log_writer("func:csrf_checker","CSRF success [".$csrf."]");
                    log_writer("func:csrf_checker","CSRF success [".$csrf_ck."]");
                }
            }
            $csrf=$csrf_ck;
            $i++;
        }
    }else{
        log_writer("func:csrf_checker","HTTP_REFERER failed \$_SERVER[".$_SERVER['HTTP_REFERER']."]");
        log_writer("func:csrf_checker","HTTP_REFERER failed ParamUrl[".ROOT_URL.$row."]");
        $chkflg = "アクセス元が不正です";
    }
    
    return $chkflg;
}
// =========================================================
// 不可逆暗号化
// =========================================================
function passEx($str,$uid,$key){
//	if(strlen($str)<=8 and !empty($uid)){
	if(strlen($str)>0 and !empty($uid)){
		$rtn = crypt($str,$key);
		for($i = 0; $i < 1000; $i++){
			$rtn = substr(crypt($rtn.$uid,$key),2);
		}
	}else{
		$rtn = $str;
	}
	return substr($rtn,0,20);
}
// =========================================================
// 可逆暗号(日本語文字化け対策)
// 22.05.11 商品名の暗号化運用を止めるため、既存関数を無効化。以降、暗号化したい場合はver2を使用する
// =========================================================

function rot13encrypt2 ($str) {
	//暗号化
    return bin2hex(openssl_encrypt($str, "AES-128-ECB", "1"));
}
function rot13decrypt2 ($str) {
	//暗号化解除
    return openssl_decrypt(hex2bin($str), "AES-128-ECB", "1");
}

// =========================================================
// XSS対策 post get を echo するときに使用
// =========================================================
function secho($s) {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

// =========================================================
// PDO の接続オプション取得
// =========================================================
function get_pdo_options() {
  return array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
               PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,   //sqlの複文禁止 "select * from hoge;delete from hoge"みたいなの
               PDO::ATTR_EMULATE_PREPARES => false);        //同上
}




// =========================================================
// GUID取得
// =========================================================
function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }
    else {
        mt_srand((int)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
    }
}

// =========================================================
// CSV出力
// =========================================================
function output_csv($data,$kikan){
    $date = date("Ymd");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=売上実績_{$date}_{$kikan}.csv");
    
    // データ行の文字コード変換・加工
    foreach ($data as $data_key => $line) {
        foreach ($line as $line_key => $value) {
            $data[$data_key][$line_key] = mb_convert_encoding($value, "SJIS", "UTF-8");
        }
    }

    foreach ($data as $key => $line) {
        echo implode($line, ",") . "\r\n";
    }
    exit;
}

// =========================================================
// 日付未指定時にルールに沿ってYMDを返す
// =========================================================
function rtn_date($date,$mode){
    //rtn_date(empty($date),$mode)
    //$date:チェックする日付　$mode:日付が空白の場合　today=今日　min=0000-00-00 max=2999-12-31 を返す
    
    if($date==false){
        //何かしら入ってる
        $rtn_date = (string)$date;
    }elseif($mode=="today"){
        $rtn_date = (string)date("Y-m-d");
    }elseif($mode=="min"){
        $rtn_date = "0000-00-00";
    }elseif($mode=="max"){
        $rtn_date = "2999-12-31";
    }else{
        $rtn_date = "";
    }
    
    return $rtn_date;
}

// =========================================================
// 検索ワード未指定時にワイルドカード(%)を返す
// =========================================================
function rtn_wildcard($word){
    //rtn_wildcard(empty($word))で使用する
    if($word==true){
        //空白の場合
        return "%";
    }else{
        return $word;
    }
}


// =========================================================
// 指定ディレクトリ内のファイル一覧取得
// =========================================================
function getFileList($dir) {
    $files = glob(rtrim($dir, '/') . '/*');
    $list = array();
    foreach ($files as $file) {
        if (is_file($file)) {
            $list[] = $file;
        }
        if (is_dir($file)) {
            $list[] = $file;
            $list = array_merge($list, getFileList($file));
        }
    }
    return $list;
}




?>