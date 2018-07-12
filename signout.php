<?php  
    session_start();

    // SESSION変数を空にした後に$_SESSION変数を破壊
    // ブラウザ内のSESSION変数の破棄（空にする）
    $_SESSION = [];

    // サーバー内の$_SESSION変数を破壊
    // session_destroy()変数：セッションに登録されているデータを全て破壊する
    session_destroy();

    header("Location: signin.php");
    exit();
?>