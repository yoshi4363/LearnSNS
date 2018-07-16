<?php  

    session_start();

    require("dbconnect.php");

    // フォローされる人のid取得
    $user_id = $_GET["user_id"];

    // フォローボタン押した人のid取得
    $follower_id = $_SESSION["id"];

    $sql = "INSERT INTO `followers` SET `id`=NULL, `user_id`=?, `follower_id`=?";
    $data = array($user_id, $follower_id);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    header("Location: profile.php?user_id=" . $user_id);
?>