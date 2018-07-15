<?php
    require("dbconnect.php");

    // sql文について
    // prepare文：SQL実行の前処理
    // execute文：準備された文の実行（execute：実行する）
    // fetch文：DB検索結果から１件取得する（取得後、次のレコードへ移動）（fetch：取ってくる）
    // PDO::FETCH_ASSOC：カラム名をキーに連想配列でデータ取得（PDO：）

    // ユーザー一覧表示のための情報取得
    $sql = "SELECT * FROM `users`";
    $data = array();
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    while (true) {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($record == false) {
            break;
        }

        // 同じコード内で複数のSQL文作る場合は変数名変える
        // 各ユーザーのつぶやき数取得
        $feed_sql = "SELECT COUNT(*) AS `feed_count` FROM `feeds` WHERE `user_id`=?";
        $feed_data = array($record["id"]);
        $feed_stmt = $dbh->prepare($feed_sql);
        $feed_stmt->execute($feed_data);
        $feed = $feed_stmt->fetch(PDO::FETCH_ASSOC);

        $record["feed_count"] = $feed["feed_count"];

        // foreachで回す用の変数用意
        $users[] = $record;
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <title>Learn SNS</title>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body style="margin-top: 60px; background-color: #E4E6EB;">
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <!-- aria-expanded属性は「要素の開閉状態」を表す true：開いている、false：閉じている -->
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse1" aria-expanded="false">
          <span class="sr-only">Togglenavigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">Learn SNS</a>
      </div>
      <div class="collapse navbar-collapse" id="navbar-collapse1">
        <ul class="nav navbar-nav">
          <li><a href="timeline.php">タイムライン</a></li>
          <li class="active"><a href="#">ユーザー一覧</a></li>
        </ul>
        <!-- role属性：役割の明確化 -->
        <form method="get" action="" class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <input type="text" name="search_word" class="form-control" placeholder="投稿を検索">
          </div>
          <button type="submit" class="btn btn-default">検索</button>
        </form>
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="" width="18" class="img-circle">test <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="signout.php">サインアウト</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <?php foreach ($users as $user) { ?>
      
    
    <div class="row">
      <div class="col-xs-12">
        <div class="thumbnail">
          <div class="row">
            <div class="col-xs-1">
              <img src="user_profile_img/<?php echo $user["img_name"]; ?>" width="80">
            </div>
            <div class="col-xs-11">
              名前 <?php echo $user["name"]; ?><br>
              <a href="#" style="color: #7F7F7F;"><?php echo $user["created"]; ?>からメンバー</a>
            </div>
          </div>
          
          <div class="row feed_sub">
            <div class="col-xs-12">
              <span class="comment_count">つぶやき数：<?php echo $user["feed_count"]; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</body>
</html>