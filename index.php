<?php
session_start();
require_once("funcs.php");

if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id()){

    $bm_move = "select_nologin.php";
    $log_inout = '
        <a href="./user_kanri/login.php" 
        class="
        mr-5
        hover:text-gray-900 hover:cursor-pointer hover:bg-indigo-300
        ">
        ログイン
        </a>
    ';
}else{

    session_regenerate_id(true);
    $_SESSION["chk_ssid"] = session_id();

    $bm_move = "select.php";
    $log_inout = '
        <a href="./user_kanri/logout.php" 
        class="
        mr-5
        hover:text-gray-900 hover:cursor-pointer hover:bg-indigo-300
        ">
        ログアウト
        </a>
    ';
}

// 管理者チェック⇒管理メニュー表示
$user_kanri = kanriCheck();


?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c117fbda8a.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>ブックマーク</title>
</head>
<body class="text-gray-600 body-font">
<header class="text-gray-600 body-font border-b border-gray-500">
    <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
            <a href="index.php" 
                class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
                <span class="ml-3 text-xl">BookMark</span>
            </a>

            <nav class="md:ml-auto flex flex-wrap items-center text-base justify-center">
                <a href="<?=$bm_move?>" 
                    class="
                        mr-5
                        hover:text-gray-900 hover:cursor-pointer hover:bg-indigo-300
                    "
                    >
                    ブックマーク一覧
                </a>
                <?=$user_kanri?>
                <?= $log_inout ?>
            </nav>
    </div>
</header>

<main>
    <section class="container px-5 py-5 mx-auto">
        <div class="flex flex-col text-center w-full mb-5">
            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-700">ブックマーク</h1>
        </div>
        <form method="POST" action="insert.php"  class="lg:w-1/2 md:w-2/3 mx-auto">
            <div class="flex flex-wrap -m-2">
                <div class="p-2 w-full">
                    
                    <label for="name" class="leading-7 text-sm text-gray-600">本のタイトル</label>
                    <input type="text" id="name" name="name" 
                        class="
                            w-full
                            bg-gray-100 bg-opacity-50
                            rounded border border-gray-300
                            focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200
                            text-base text-gray-700
                            py-1 px-3
                        " 
                        required
                    >

                </div>
                <div class="p-2 w-full">
            
                    <label for="author" class="leading-7 text-sm text-gray-600">著者</label>
                    <input type="text" id="author" name="author" 
                        class="
                            w-full
                            bg-gray-100 bg-opacity-50
                            rounded border border-gray-300
                            focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200
                            text-base text-gray-700
                            py-1 px-3
                        " 
                        required
                    >
            
                </div>
                
                <div class="p-2 w-full">
            
                    <label for="url" class="leading-7 text-sm text-gray-600">url</label>
                    <input type="text" id="url" name="url" 
                        class="
                            w-full
                            bg-gray-100 bg-opacity-50
                            rounded border border-gray-300
                            focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200
                            text-base text-gray-700
                            py-1 px-3
                        "
                    >
            
                </div>
                <div class="p-2 w-full">

                    <label for="comment" class="leading-7 text-sm text-gray-600">コメント</label>
                    <textarea id="comment" name="comment" 
                        class="
                            w-full
                            bg-gray-100 bg-opacity-50
                            rounded border border-gray-300
                            focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200
                            h-32
                            text-base text-gray-700
                            py-1 px-3
                            resize-none
                        "
                    ></textarea>
                </div>
            </div>

            <input type="submit" value="送信" 
                class="
                    flex
                    mx-auto mt-5
                    text-white text-lg
                    bg-indigo-500
                    rounded border-0
                    py-2 px-8
                    focus:outline-none hover:bg-indigo-600
                "
            >
        </form>
    </section>

    <section id="search_result" class="lg:w-3/4 md:w-2/3 mx-auto container px-5 py-5 mx-auto">
        <div class="flex flex-col text-center w-full mb-5">
            <h1 id="result_title" 
                class="
                    sm:text-3xl
                    text-2xl font-medium
                    title-font
                    mb-4
                    text-gray-700
                " 
                style="display: none;"
            >検索結果</h1>
        </div>
        <ul id="results" class="flex flex-wrap justify-center"></ul>

    </section>

</main>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<script>
const gBooksAPI = "https://www.googleapis.com/books/v1/volumes?q=";

function searchAPI(val){
    $.get(gBooksAPI + $(this).val() + "&maxResults=30").done(function(data){
        
        $("#result_title").css("display", "block");
        $("#results").empty();

        let html = "";
        data.items.forEach(item => {
            
            const title = item.volumeInfo.title;
            const author = item.volumeInfo.authors ?? "不明";
            const url = item.volumeInfo.previewLink ?? "Linkが見つかりません";
            let thumbnail = "";

            if(!item.volumeInfo.imageLinks){
                thumbnail = "./img/icons_noimg.png"
            }else{
                thumbnail = item.volumeInfo.imageLinks.thumbnail
            }


            html += `<div 
                        class="
                            m-1 p-1
                            rounded border border-gray-300
                            w-1/5 
                            text-center
                            hover:bg-indigo-100 hover:cursor-pointer
                            searchItem
                        "
                    >
                <div class="text-xs title">${title}</div>
                <div class="text-xs author">${author}</div>
                <div class="w-5/6 mx-auto thumbnail"><img src="${thumbnail}" class="text-center mx-auto"></div>
                <div style="display:none" class="link"><a href="${url}">リンク</a></div>
            </div>`

        });
        $("#results").append(html);
    })
}


$("#name").on("change", searchAPI);
$("#author").on("change", searchAPI);

$("#results").on("click", ".searchItem", function(){

    const title = $(this).children(".title").contents().text();
    const author = $(this).children(".author").contents().text();
    const url = $(this).children(".link").children("a").attr("href");
    // const thumbnail = $(this).children(".thumbnail").children("img").attr("src");

    $("#name").val(title);
    $("#author").val(author);
    $("#url").val(url);
})

</script>




</body>
</html>