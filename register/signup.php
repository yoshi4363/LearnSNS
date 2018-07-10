<?php
    session_start();

    // $errors[] = ""; ではダメ 
    $errors = array();

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

        // 「file」は$_POSTで受け取れない（$_FILESを使う）
        // $_FILES["キー"]["name"]; で画像名取得
        // $_FILES["キー"]["tmp_name"]; 画像そのものを取得（テンポラリーファイル）
        // 画像取得ルール。inputタグにてtype = "file" になっている、formタグにてenctype属性にmultipart/form-dataが指定されている、POST送信になっている。
        $file_name = $_FILES["input_img_name"]["name"];
        if (!empty($file_name)) {
            // substr関数：第一引数から文字列を取得
            $file_type = substr($file_name, -4);
            // strtolower関数：文字列を小文字にする
            $file_type = strtolower($file_type);
            if ($file_type != ".jpg" && $file_type != ".png" && $file_type != ".gif" && $file_type != "jpeg") {
                $errors["img_name"] = "type";
            }
        }else{
            $errors["img_name"] = "blank";
        }

        // エラーがなかった際の処理
        if (empty($errors)) {
            // date_default_timezone_setでデフォの時刻設定をする、まず
            date_default_timezone_set("Asia/Tokyo");
            // date(); にて時刻フォーマット指定 YmdHis：秒単位まで取得（20180711171332 のように取得）
            $date_str = date("YmdHis");
            $submit_file_name = $date_str . $file_name;

            // move_uploaded_file()関数：ブラウザに一時的に保存された画像データ（テンポラリーファイル）と、アップロード先のパスを指定することでデータをアップロードする機能を持った関数
            // 構文：move_uploaded_file(テンポラリーファイル, アップロード先パス)
            // テンポラリーファイルは$_FILES["キー"]["tmp_name"]で取得、キーはinput_img_name
            // 画像のup時に指定した一意のファイル名に変更するためパス指定時に . $submit_file_name を繋げる
            move_uploaded_file($_FILES["input_img_name"]["tmp_name"], "../user_profile_img/" . $submit_file_name);

            // SESSIONはサーバー内全てで共通しているため、重複を防ぐため多次元配列化（registerキーを設置）
            $_SESSION["register"]["name"] = $_POST["input_name"];
            $_SESSION["register"]["email"] = $_POST["input_email"];
            $_SESSION
            ["register"]["password"] = $_POST["input_password"];
            $_SESSION["register"]["img_name"] = $submit_file_name;

            header("Location: check.php");
            exit();
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
            <!-- accept属性にてimage指定すると画像しかupできなくなる -->
            <input type="file" name="input_img_name" id="img_name" accept="image/*">
            <?php if (isset($errors["img_name"]) && $errors["img_name"] == "blank") { ?>
              <p class="text-danger">画像を選択して下さい。</p>
            <?php } ?>
            <?php if (isset($errors["img_name"]) && $errors["img_name"] == "type") { ?>
              <p class="text-danger">拡張子が「jpg」「jpeg」「png」「gif」の画像を選択して下さい。</p>
            <?php } ?>
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