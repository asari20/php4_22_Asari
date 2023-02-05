<?php


// SESSION開始
session_start();

// 関数呼び出し
require_once('funcs.php');

// ログインチェック
loginCheck();

// 1.DB接続
$pdo = db_conn();

// 2.対象のID取得
$key = $_GET['unique_key'];

// 3.削除SQL
$stmt = $pdo->prepare("DELETE FROM gs_bm_table WHERE unique_key = :key");
$stmt -> bindValue(':key', $key, PDO::PARAM_INT);
$status = $stmt -> execute();

// 4.データ削除処理後
if($status == false){
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("ErrorMassage:".$error[2]);
}else{
    redirect('select.php');
}



















?>