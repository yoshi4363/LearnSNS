<?php
    session_start();
    require("dbconnect.php");

    $sql = "SELECT * FROM `users` WHERE `id`=?";
    $data = array($_SESSION["id"]);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);

    $errors = array();

    if (!empty($_POST["feed"])) {
        $feed = $_POST["feed"];
        if (isset($feed) && $feed == "") {
            $errors["feed"] = "blank";
        }else{
            $sql = "INSERT INTO `feeds` SET `feed`=?, `user_id`=?, `created`=NOW()";
            $data = array($feed, $_SESSION["id"]);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);

            header("Location: timeline.php");
            exit();
        }
    }

    // ページネーション処理
    $page = ""; // ページ番号（何ページ目か）が入る変数
    $page_row_number = 5; // １ページに表示するデータの数

    if (isset($_GET["page"])) {
        $page = $_GET["page"];
    }else{
        // GET送信されてるページ数がないなら１ページ目とみなす
        $page = 1;
    }

    // max関数：引数の中で最大の値を返す
    $page = max($page,1);

    // 投稿データ数から最大ページ数を計算する
    $count_sql = "SELECT COUNT(*) AS `count` FROM `feeds`";
    $count_data = array();
    $count_stmt = $dbh->prepare($count_sql);
    $count_stmt->execute($count_data);

    $count_record = $count_stmt->fetch(PDO::FETCH_ASSOC);

    // ページ数の計算
    // ceil関数：小数点を切り上げる
    $all_page_number = ceil($count_record["count"] / $page_row_number);

    // min関数：引数の中で最小の値を返す（max関数の逆）
    $page = min($page,$all_page_number);

    // データを取得する開始番号の計算
    $start = ($page -1)*$page_row_number;
    // ページネーション処理終了

    // 検索ボタンが押された際に「あいまい検索」
    // 検索ボタンにてsubmitされたらsearch_wordにてPOST送信されてくる
    if (isset($_POST["search_word"]) == true) {
        // あいまい検索用のSQL文（LIKE演算子）
        $sql = "SELECT `f`.*, `u`.`name`, `u`.`img_name` FROM `feeds` AS `f` LEFT JOIN `users` AS `u` ON `f`.`user_id` = `u`.`id` WHERE `f`.`feed` LIKE '%".$_POST['search_word']."%' ORDER BY `f`.`created` DESC";
    }else{
        // 通常時（検索ボタンが押されてない時）は全件取得
        // LIMIT句：SELECT時に取得するデータの件数に上限を設ける（ORDER BY句の後に記述）
        // 構文：LIMIT 取得開始番号（０から始まる）,取得したい件数
        $sql = "SELECT `f`.*, `u`.`name`, `u`.`img_name` FROM `feeds` AS `f` LEFT JOIN `users` AS `u` ON `f`.`user_id` = `u`.`id` WHERE 1 ORDER BY `f`.`created` DESC LIMIT $start,$page_row_number";
    }

    $data = array();
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    
    // 表示用の配列を用意
    $feeds = array();

    while (true) {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record == false) {
            break;
        }

        $comment_sql = "SELECT `c`.*, `u`.`name`, `u`.`img_name` FROM `comments` AS `c` LEFT JOIN `users` AS `u` ON `c`.`user_id` = `u`.`id` WHERE `feed_id`=?";
        $comment_data = array($record["id"]);
        $comment_stmt = $dbh->prepare($comment_sql);
        $comment_stmt->execute($comment_data);

        // 空の配列用意
        $comments_array = array();

        while (true) {
            $comment_record = $comment_stmt->fetch(PDO::FETCH_ASSOC);
            if ($comment_record == false) {
                break;
            }
            $comments_array[] = $comment_record;
        }

        $record["comments"] = $comments_array;

        // いいね数を取得するコード
        $like_sql = "SELECT COUNT(*) AS `like_count` FROM `likes` WHERE `feed_id`=?";
        $like_data = array($record["id"]);
        $like_stmt = $dbh->prepare($like_sql);
        $like_stmt->execute($like_data);
        $like = $like_stmt->fetch(PDO::FETCH_ASSOC);
        $record["like_count"] = $like["like_count"];

        // いいね済みか判断するコード
        $like_flag_sql = "SELECT COUNT(*) AS `like_flag` FROM `likes` WHERE `user_id`=? AND `feed_id`=?";
        $like_flag_data = array($_SESSION["id"], $record["id"]);
        $like_flag_stmt = $dbh->prepare($like_flag_sql);
        $like_flag_stmt->execute($like_flag_data);
        $like_flag_likes = $like_flag_stmt->fetch(PDO::FETCH_ASSOC);

        if ($like_flag_likes["like_flag"] > 0) {
            $record["like_flag"] = 1;
        }else{
            $record["like_flag"] = 0;
        }

        // 「いいね！済み」が押された際に、既にいいねされているものだけを配列に代入する
        if (isset($_GET["feed_select"]) && $_GET["feed_select"] == "likes" && $record["like_flag"] == 1) {
            $feeds[] = $record;
        // 「いいね！済み」押されてない時は全件表示
        }elseif(!isset($_GET["feed_select"])){
            $feeds[] = $record;
        }

        // 新着順が押された時は全件表示
        if (isset($_GET["feed_select"]) && $_GET["feed_select"] == "news") {
            $feeds[] = $record;
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Learn SNS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body style="margin-top: 60px; background: #E4E6EB;">
  <div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Learn SNS</a>
      </div>
      <div class="collapse navbar-collapse" id="navbar-collapse1">
        <ul class="nav navbar-nav">
          <li class="active"><a href="#">タイムライン</a></li>
          <li><a href="user_index.php">ユーザー一覧</a></li>
        </ul>
        <form method="POST" action="" class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <input type="text" name="search_word" class="form-control" placeholder="投稿を検索">
          </div>
          <button type="submit" class="btn btn-default">検索</button>
        </form>
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="user_profile_img/<?php echo $signin_user["img_name"]; ?>" width="18" class="img-circle"><?php echo $signin_user["name"]; ?><span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#">マイページ</a></li>
              <li><a href="signout.php">サインアウト</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-xs-3">
        <ul class="nav nav-pills nav-stacked">
          <?php if (isset($_GET["feed_select"]) && $_GET["feed_select"] == "likes") { ?>
            <li><a href="timeline.php?feed_select=news">新着順</a></li>
            <li class="active"><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
          <?php }else{ ?>
            <li class="active"><a href="timeline.php?feed_select=news">新着順</a></li>
            <li><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
          <?php } ?>
          <!-- <li><a href="timeline.php?feed_select=follows">フォロー</a></li> -->
        </ul>
      </div>
      <div class="col-xs-9">
        <div class="feed_form thumbnail">
          <form method="POST" action="">
            <div class="form-group">
              <textarea name="feed" class="form-control" rows="3" placeholder="Happy Hacking!" style="font-size: 24px;"></textarea><br>
              <?php if (isset($errors["feed"]) && $errors["feed"] == "blank") { ?>
                <p class="alert alert-danger">投稿内容を入力して下さい。</p>
              <?php } ?>
            </div>
            <input type="submit" value="投稿する" class="btn btn-primary">
          </form>
        </div>

        <!-- 繰り返し処理 -->
        <?php foreach($feeds as $feed) { ?>
          <div class="thumbnail">
            <div class="row">
              <div class="col-xs-1">
                <img src="user_profile_img/<?php echo $feed["img_name"]; ?>" width="40">
              </div>
              <div class="col-xs-11">
                <?php echo $feed["name"]; ?><br>
                <a href="#" style="color: #7F7F7F;"><?php echo $feed["created"]; ?></a>
              </div>
            </div>
            <div class="row feed_content">
              <div class="col-xs-12" >
                <span style="font-size: 24px;"><?php echo $feed["feed"]; ?></span>
              </div>
            </div>
            <div class="row feed_sub">
              <div class="col-xs-12">
                <?php if ($feed["like_flag"] == 0) { ?>
                <form method="POST" action="like.php" style="display: inline;">
                  <input type="hidden" name="feed_id" value="<?php echo $feed['id']; ?>">
                    <input type="hidden" name="like" value="like">
                    <button type="submit" class="btn btn-default btn-xs"><i class="fa fa-thumbs-up" aria-hidden="true"></i>いいね！</button>
                </form>
                <?php }else{ ?>
                <form method="POST" action="unlike.php" style="display: inline;">
                  <input type="hidden" name="feed_id" value="<?php echo $feed['id']; ?>">
                    <input type="hidden" name="like" value="like">
                    <button type="submit" class="btn btn-default btn-xs"><i class="fa fa-thumbs-up" aria-hidden="true"></i>いいね！を取り消す</button>
                </form>
                <?php } ?>
                <?php if ($feed["like_count"] > 0) { ?>
                  <span class="like_count">いいね数 : <?php echo $feed["like_count"]; ?></span>
                <?php } ?>

                <a href="#collapseComment<?php echo $feed["id"]; ?>" data-toggle="collapse" aria-expanded="false">
                  <?php if ($feed["comment_count"] == 0) { ?>
                    <span class="comment_count">コメント</span>
                  <?php }else{ ?>
                    <span class="comment_count">コメント数 : <?php echo $feed["comment_count"]; ?></span>
                  <?php } ?>
                </a>

                <?php if ($feed["user_id"] == $_SESSION["id"]) { ?>
                  <a href="edit.php?feed_id=<?php echo $feed['id']; ?>" class="btn btn-success btn-xs">編集</a>
                  <!-- confirm()：確認ダイアログ表示（括弧内の文字が表示される） -->
                  <a onclick="return confirm('ほんとに消すの？')" href="delete.php?feed_id=<?php echo $feed['id']; ?>" class="btn btn-danger btn-xs">削除</a>
                  <?php } ?>
              </div>
              <?php include("comment_view.php"); ?>
            </div>
          </div>
        <?php } ?>
        <!-- 繰り返し終了 -->

        <div aria-label="Page navigation">
          <ul class="pager">

            <?php if ($page == 1) { ?>
              <li class="previous disabled"><a href="#"><span aria-hidden="true">&larr;</span> Older</a></li>
            <?php }else{ ?>
              <li class="previous"><a href="timeline.php?page=<?php echo $page-1; ?>"><span aria-hidden="true">&larr;</span> Older</a></li>
            <?php } ?>

            <?php if ($page == $all_page_number) { ?>
              <li class="next disabled"><a href="#">Newer <span aria-hidden="true">&rarr;</span></a></li>
            <?php }else{ ?>
              <li class="next"><a href="timeline.php?page=<?php echo $page+1; ?>">Newer <span aria-hidden="true">&rarr;</span></a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>