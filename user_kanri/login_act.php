<?php
// SESSION開始
session_start();

// 1.POST取得
$lid = $_POST["lid"];
$lpw = $_POST["lpw"];

// 2.DB接続
require_once("../funcs.php");
$pdo = db_conn();

// 3.SQL用意
$stmt = $pdo->prepare("SELECT * FROM gs_user_table
    WHERE lid = :lid
");

$stmt -> bindValue(':lid',$lid,PDO::PARAM_STR);
$status = $stmt->execute();

// 4.SQL実行時エラーがある場合はSTOP
if($status == false){
    sql_error($stmt);
}

// 5.取得データを表示
$val = $stmt->fetch();
var_dump($val);
$pw = password_hash($lpw,PASSWORD_DEFAULT);
echo $pw;
if(password_verify($lpw, $val["lpw"])){
    // login成功時
    $_SESSION["chk_ssid"]  = session_id();//SESSION変数にIDを保存
    $_SESSION["kanri_flg"] = $val["kanri_flg"];//SESSION変数に管理者権限のflagを保存
    $_SESSION['name']      = $val['name'];//SESSION変数にnameを保存

}else{
    // redirect("logout.php");
}



?>
