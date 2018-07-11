<?php  
    
    // DB接続
    $dsn = "mysql:dbname=LearnSNS_review;host=localhost";
    $user = "root";
    $password = "";
    $dbh = new PDO($dsn, $user, $password);
    // SQL文にエラーがあった際に、画面にエラーを出す設定
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->query("SET NAMES utf8");
?>