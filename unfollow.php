<?php  

    session_start();

    require("dbconnect.php");

    // フォローされている人のid取得
    $user_id = $_GET["user_id"];

    // フォロー解除ボタン押した人のid取得
    $follower_id = $_SESSION["id"];

    $sql = "DELETE FROM `followers` WHERE `user_id`=? AND `follower_id`=?";
    $data = array($user_id, $follower_id);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    header("Location: profile.php?user_id=" . $user_id);
?>