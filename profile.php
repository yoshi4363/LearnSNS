<?php
    session_start();

    require("dbconnect.php");

    if (isset($_GET["user_id"])) {
        // URL指定 or user_indexのリンクからアクセスされた場合に$_GET["user_id"]にて値を取得する
        $user_id = $_GET["user_id"];
    }else{
        $user_id = $_SESSION["id"];
    }

    $signin_sql = "SELECT * FROM `users` WHERE `id`=?";
    $signin_data = array($_SESSION["id"]);
    $signin_stmt = $dbh->prepare($signin_sql);
    $signin_stmt->execute($signin_data);
    $signin_user = $signin_stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM `users` WHERE `id`=?";
    $data = array($user_id);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $profile_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Followingの取得
    $following_sql = "SELECT `fw`.*, `u`.`name`, `u`.`img_name`, `u`.`created` FROM `followers` AS `fw` LEFT JOIN `users` AS `u` ON `fw`.`user_id` = `u`.`id` WHERE `follower_id`=?";
    $following_data = array($user_id);
    $following_stmt = $dbh->prepare($following_sql);
    $following_stmt->execute($following_data);

    $following = array();

    while (true) {
        $following_record = $following_stmt->fetch(PDO::FETCH_ASSOC);

        if ($following_record == false) {
            break;
        }
        $following[] = $following_record;
    }

    // Followersの取得
    $followers_sql = "SELECT `fw`.*, `u`.`name`, `u`.`img_name`, `u`.`created` FROM `followers` AS `fw` LEFT JOIN `users` AS `u` ON `fw`.`follower_id` = `u`.`id` WHERE `user_id`=?";
    $followers_data = array($user_id);
    $followers_stmt = $dbh->prepare($followers_sql);
    $followers_stmt->execute($followers_data);

    $followers = array();

    // サインインユーザーが表示ユーザーをフォローしていたら１、していないなら０
    $follow_flag = 0;

    while (true) {
        $followers_record = $followers_stmt->fetch(PDO::FETCH_ASSOC);

        if ($followers_record == false) {
            break;
        }

        // フォロワーの中にサインインしている人がいるかどうかのチェック
        if ($followers_record["follower_id"] == $_SESSION["id"]) {
            $follow_flag = 1;
        }
        $followers[] = $followers_record;
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

  <!-- 使い回しのナビバー（ナビゲーションバー）を読み込み。手間省略。 -->
  <?php include("navbar.php"); ?>
  
  <div class="container">
    <div class="row">
      <div class="col-xs-3 text-center">
        <img src="user_profile_img/<?php echo $profile_user["img_name"]; ?>" class="img-thumbnail" />
        <h3><?php echo $profile_user["name"]; ?></h3>

        <?php if ($user_id != $_SESSION["id"]) { ?>
          <?php if ($follow_flag == 0) { ?>
            <a href="follow.php?user_id=<?php echo $profile_user['id']; ?>"><button class="btn btn-default btn-block">フォローする</button></a>
          <?php }else{ ?>
            <a href="unfollow.php?user_id=<?php echo $profile_user['id']; ?>"><button class="btn btn-default btn-block">フォロー解除する</button></a>
          <?php } ?>
        <?php } ?>
      </div>

      <div class="col-xs-9">
        <ul class="nav nav-tabs">
          <li class="active">
            <a href="#tab1" data-toggle="tab">Followers</a>
          </li>
          <li>
            <a href="#tab2" data-toggle="tab">Following</a>
          </li>
        </ul>

        <!-- Followersの中身 -->
        <div class="tab-content">
          <div id="tab1" class="tab-pane fade in active">
            <?php foreach ($followers as $follower) { ?>
              <div class="thumbnail">
                <div class="row">
                  <div class="col-xs-2">
                    <img src="user_profile_img/<?php echo $follower["img_name"]; ?>" width="80">
                  </div>
                  <div class="col-xs-10">
                    名前 <?php echo $follower["name"]; ?><br>
                    <a href="#" style="color: #7F7F7F;"><?php echo $follower["created"]; ?>からメンバー</a>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>

          <!-- Followingの中身 -->
            <div id="tab2" class="tab-pane fade">
              <?php foreach ($following as $following_user) { ?>
                <div class="thumbnail">
                  <div class="row">
                    <div class="col-xs-2">
                      <img src="user_profile_img/<?php echo $following_user["img_name"]; ?>" width="80">
                    </div>
                    <div class="col-xs-10">
                      名前 <?php echo $following_user["name"]; ?><br>
                      <a href="#" style="color: #7F7F7F;"><?php echo $following_user["created"]; ?>からメンバー</a>
                    </div>
                  </div>
                </div>
              <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>