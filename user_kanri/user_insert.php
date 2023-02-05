<?php
// 1.POSTデータ取得
$name = $_POST['name'];
$lid = $_POST['lid'];
$lpw = $_POST['lpw'];
$kanri_flg = $_POST['kanri_flg'];
$life_flg = $_POST['life_flg'];


// 1.5 pwハッシュ化
$pw = password_hash($lpw,PASSWORD_DEFAULT);

// 2.DB接続
require_once("../funcs.php");
$pdo = db_conn();

// 3.SQL文用意（update）
$stmt = $pdo->prepare(
    "INSERT INTO gs_user_table(id,name,lid,lpw,kanri_flg,life_flg)
    VALUES(NULL, :name, :lid, :pw, $kanri_flg, $life_flg)

");
// bindせずにハッシュ後のpwを入れてもエラーになる。
// ハッシュ後もbindは必要？？


$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->bindValue(':pw', $pw, PDO::PARAM_STR);

$status = $stmt->execute();


// 4.データ登録
if($status == false){
    $error = $stmt->errorInfo();
    exit("ErrorMassage:".$error[2]);
}else{
    //５．select.phpへリダイレクト
    redirect('user_select.php');
}



?>