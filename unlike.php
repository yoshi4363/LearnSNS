<?php  
    session_start();

    require("dbconnect.php");

    $feed_id = $_POST["feed_id"];

    $sql = "DELETE FROM `likes` WHERE `user_id`=? AND `feed_id`=?";
    $data = array($_SESSION["id"], $feed_id);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    header("Location: timeline.php");
?>