<?php
    session_start();
    require("dbconnect.php");
    
    $errors = array();

    if (!empty($_POST)) {
        $email = $_POST["input_email"];
        $password = $_POST["input_password"];

        if ($email != "" && $password != "") {
            $sql = "SELECT * FROM `users` WHERE `email`=?";
            $data = array($email);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($rec == false) {
                $errors["signin"] = "failed";
            }

            // password_verify()関数：第一引数に比較したい文字列、第二引数にハッシュ化されたパスワードを入力。
            if (password_verify($password, $rec["password"])) {
                $_SESSION["id"] = $rec["id"];

                header("Location: timeline.php");
                exit();
            }
        }else{
            $errors["signin"] = "blank";
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
<body style="margin-top: 60px">
  <div class="container">
    <div class="row">
      <div class="col-xs-8 col-xs-offset-2 thumbnail">
        <h2 class="text-center content_header">サインイン</h2>
        <form method="POST" action="" enctype="multipart/form-data">
          <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com">
            <?php if (isset($errors["signin"]) && $errors["signin"] == "blank") { ?>
              <p class="text-danger">メールアドレスとパスワードを入力して下さい。</p>
            <?php } ?>
            <?php if (isset($errors["signin"]) && $errors["signin"] == "failed") { ?>
              <p class="text-danger">サインインに失敗しました。</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="input_password" class="form-control" id="password" placeholder="4 ~ 16文字のパスワード">
          </div>
          <input type="submit" class="btn btn-info" value="サインイン">
          <a href="register/signup.php" style="float: right; padding-top: 6px;" class="text-success">新規登録</a>
        </form>
      </div>
    </div>
  </div>
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>