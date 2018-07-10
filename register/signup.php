<?php  
    $errors[] = "";

    if (!empty($_POST)) {
        $name = $_POST["input_name"];
        $email = $_POST["input_email"];
        $password = $_POST["input_password"];

        if ($name == "") {
            $errors["name"] = "blank";
        }

        if ($email == "") {
            $errors["email"] = "blank";
        }

        // strlen（string lengthの略）関数：文字列の長さ（文字数）を取得する
        $count = strlen($password);

        if ($password == "") {
            $errors["password"] = "blank";
        }elseif ($count < 4 || 16 < $count) {
            $errors["password"] = "length"; 
        }
    }
?>





<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Learn SNS</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>
<body style="margin-top: 60px">
  <div class="container">
    <div class="row">
      <div class="col-xs-8 col-xs-offset-2 thumbnail">
        <h2 class="text-center content_header">アカウント作成</h2>
        <form method="post" action="signup.php" enctype="multipart/form-data">
          <div class="form-group">
            <!-- forの値とid属性の値を同じにして紐付け -->
            <label for="name">ユーザー名</label>
            <input type="text" name="input_name" class="form-control" id="name" placeholder="山田 太郎">
            <!-- isset：変数に値がセットされているかつNULLでないことを調べる -->
            <?php if (isset($errors["name"]) && $errors["name"] == "blank") { ?>
              <p class="text-danger">ユーザー名を入力して下さい。</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com">
            <?php if (isset($errors["email"]) && $errors["email"] == "blank") { ?>
              <p class="text-danger">メールアドレスを入力して下さい。</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="input_password" class="form-control" id="password" placeholder="４〜１６文字のパスワード">
            <?php if (isset($errors["password"])  && $errors["password"] == "blank") { ?>
              <p class="text-danger">パスワードを入力して下さい。</p>
            <?php } ?>
            <?php if (isset($errors["password"]) && $errors["password"] == "length") { ?>
              <p class="text-danger">パスワードは４〜１６文字で入力して下さい。</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="img_name">プロフィール画像</label>
            <input type="file" name="input_img_name" id="img_name">
          </div>
          <input type="submit" name="" class="btn btn-default" value="確認">
          <a href="../signin.php" style="float: right; padding-top: 6px;" class="text-success">サインイン</a>
        </form>
      </div>
    </div>
  </div>
  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
</body>
</html>