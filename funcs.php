<?php
//共通に使う関数を記述

//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){ return htmlspecialchars($str, ENT_QUOTES); }

// データベース接続関数
function db_conn(){
  try {
    $db_name = "gd_kadai_db";    //データベース名
    $db_id   = "root";      //アカウント名
    $db_pw   = "";      //パスワード：XAMPPはパスワードなしMAMPのパスワードはroot
    $db_host = "localhost"; //DBホスト
    $db_port = "3306"; //XAMPPの管理画面からport番号確認
    $pdo = new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host.';port='.$db_port.'', $db_id, $db_pw);
    return $pdo;//ここを追加！！
  } catch (PDOException $e) {
      exit('DB Connection Error:' . $e->getMessage());
  }
}

// SQLエラー
function sql_error($stmt){
  // execute(SQL実行時)にエラーがある場合
  $error = $stmt->errorInfo();
  exit("SQLError:".$error[2]);
}

// リダイレクト用関数
function redirect($file_name){
  header('location:'.$file_name);
  exit();
}

// ログインチェック
function loginCheck(){
  if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id()){

    exit("Login Error");

  }else{

    session_regenerate_id(true);
    $_SESSION["chk_ssid"] = session_id();

  }
}

function kanriCheck(){
  if(!isset($_SESSION["kanri_flg"])){
    return $user_kanri ="";
  }else{
    if($_SESSION["kanri_flg"] == 0){
      return $user_kanri ="";
    }else{
      return $user_kanri ='
      <a href="./user_kanri/user_select.php" 
      class="
          mr-5
          hover:text-gray-900 hover:cursor-pointer hover:bg-indigo-300
      ">
      ユーザー管理
      </a>
      ';
    }
  }
}

?>