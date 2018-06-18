<?php
  // $_SESSIONを使用するために必要
  session_start();

  // 違う階層にあるため
  require('db_connect.php');

  echo '<br>';
  echo '<br>';
  // var_dump($_SESSION);
  // var_dump($_COOKIE);

  // クッキーに情報が存在していたら自動ログイン
  // $_POSTにCOOKIEに保存されている値を代入
  if (isset($_COOKIE['email'])) {
    $_POST['email'] = $_COOKIE['email'];
    $_POST['password'] = $_COOKIE['password'];

    // COOKIEが保存されていたらログインした時間を更新する = 保存期間を更新する
    $_POST['save'] = 'on';
  }

  // ログインボタンが押された時、または自動ログインしている時
  if (!empty($_POST)) {
    // POST送信されたメールアドレスとパスワードと一致するものをmembersテーブルから取得する
    // WHEREで複数条件をつけるときはANDまたはORを使用する
    $sql = 'SELECT * FROM `members` WHERE `email`=? AND `password`=?';
    // 入力されている値は暗号化されていないのでもう一度暗号化しないと一致しない。
    $data = array($_POST['email'], sha1($_POST['password']));
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    // membersテーブルからデータを取得
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    // var_dump($member);

    // 一致するデータがない場合はfalseを返すのでエラーを表示させる
    if ($member == false) {
      // 一致しないとき
      $error['login'] = 'failed';
    } else {
      // 一致したとき
      // 1.セッションに会員のidを保存する
      $_SESSION['login_id'] = $member['member_id'];

      // 2.セッションにログインした時間を保存する
      // time() == 実行した時の時間を取得する
      $_SESSION['time'] = time();

      // 3.自動ログインの処理
      if ($_POST['save'] == 'on') {
        // クッキーにログイン情報を保存する
        // setcookie(保存したい名前, 保存したい値, 保存したい期間(秒数表示))
        setcookie('email', $_POST['email'], time()+60*60*24*14);
        setcookie('password', $_POST['password'], time()+60*60*24*14);
      }

      // 4.ログイン後の画面に移動
      header('Location: index.php');
    }
  }

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>ログイン</legend>
        <form method="post" action="" class="form-horizontal" role="form">
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="">
            </div>
          </div>
          <!-- 自動ログイン -->
          <div class="form-group">
            <label class="col-sm-4 control-label">自動ログイン</label>
            <div class="col-sm-8">
              <input type="checkbox" name="save" value="on">
            </div>
          </div>
          <!-- ログイン失敗したとき -->
          <?php if (isset($error['login']) && $error['login'] == 'failed') { ?>
            <p class="error">メールアドレス、またはパスワードが一致しません。</p>
          <?php } ?>
          <input type="submit" class="btn btn-default" value="ログイン"> &nbsp;|&nbsp;
          <a href="join/index.php" class="btn btn-default">会員登録</a>
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
