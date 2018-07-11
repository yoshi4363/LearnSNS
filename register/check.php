<?php  
    session_start();
    // require先のコード全行を置き換える処理
    require("../dbconnect.php");

    if (!isset($_SESSION["register"])) {
        header("Location: signup.php");
        exit();
    }

    // ①
    $name = $_SESSION["register"]["name"];
    $email = $_SESSION["register"]["email"];
    $password = $_SESSION["register"]["password"];
    $img_name = $_SESSION["register"]["img_name"];

    // submitされた時の処理
    if (!empty($_POST)) {
      // NOW()：MYSQLの関数。日付と時刻を取得する。
        $sql = "INSERT INTO `users` SET `name`=?, `email`=?, `password`=?, `img_name`=?, `created`=NOW()";
        // password_hash()関数：第一引数にハッシュ化したい文字列、第二引数にPASSWORD_DEFAULTを記述する
        $data = array($name, $email, password_hash($password, PASSWORD_DEFAULT), $img_name);
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);

        // unset()関数：指定した変数や配列を破棄する
        unset($_SESSIOON["register"]);
        header("Location: thanks.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>LearnSNS</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>
<body style="margin-top: 60px;">
  <div class="container">
    <div class="row">
      <div class="col-xs-8 col-xs-offset-2 thumbnail">
        <h2 class="text-center content_header">アカウント情報確認</h2>
        <div class="row">
          <div class="col-xs-4">
            <img src="../user_profile_img/<?php echo htmlspecialchars($img_name); ?>" class="img-responsive img-thumbnail">
          </div>
          <div class="col-xs-8">
            <div>
              <span>ユーザー名</span>
              <p class="lead"><?php echo htmlspecialchars($name); ?></p>
            </div>
            <div>
              <span>メールアドレス</span>
              <p class="lead"><?php echo htmlspecialchars($email); ?></p>
            </div>
            <div>
              <span>パスワード</span>
              <!-- ② -->
              <p class="lead">●●●●●●●●</p>
            </div>
            <!-- ③ -->
            <form method="post" action="">
              <!-- ④ -->
              <a href="signup.php?action=rewrite" class="btn btn-default">&laquo;&nbsp;戻る</a>
              <!-- ⑤ -->
              <input type="hidden" name="hogehoge" value="hogehoge">
              <input type="submit" name="" class="btn btn-primary" value="ユーザー登録">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
</body>
</html>