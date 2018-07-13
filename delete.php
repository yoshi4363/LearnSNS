<?php
    require("dbconnect.php");

    $feed_id = $_GET["feed_id"];

    $sql = "DELETE FROM `feeds` WHERE `id`=?";

    $data = array($feed_id);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    header("Location: timeline.php");

    // 削除時はDELETE処理したらすぐheader。HTML記述不要。
?>