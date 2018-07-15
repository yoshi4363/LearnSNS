<?php  
    session_start();

    require("dbconnect.php");

    $login_user_id = $_SESSION["id"];
    $comment = $_POST["write_comment"];
    $feed_id = $_POST["feed_id"];

    // コメントをInsertする処理
    $sql = "INSERT INTO `comments` SET `comment`=?, `user_id`=?, `feed_id`=?, `created`=NOW()";
    $data = array($comment, $login_user_id, $feed_id);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    // feedsテーブルにcommentの数をアップデートする処理
    $updata_sql = "UPDATE `feeds` SET `comment_count` = `comment_count`+1 WHERE `id`=?";
    $updata_data = array($feed_id);
    $updata_stmt = $dbh->prepare($updata_sql);
    $updata_stmt->execute($updata_data);

    header("Location: timeline.php");
?>