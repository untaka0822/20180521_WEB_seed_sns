<?php
  // $_SESSIONを使用するために必要
  session_start();

  // 違う階層にあるため../を使用
  require('../db_connect.php');

  echo '<br>';
  echo '<br>';
  // var_dump($_SESSION);

  // サニタイジング = 消毒するという意味
  $nickname = htmlspecialchars($_SESSION['join']['nickname']);
  $email = htmlspecialchars($_SESSION['join']['email']);
  $password = htmlspecialchars($_SESSION['join']['password']);
  $picture_path = $_SESSION['join']['picture_path'];

  // 会員登録ボタンが押された時
  if (!empty($_POST)) {
    // ユーザー情報を登録
    $sql = 'INSERT INTO `members` SET `nickname`=?, `email`=?, `password`=?, `picture_path`=?, `created`=NOW()';
    $data = array($nickname, $email, sha1($password), $picture_path);
    // sha1() == 値を16進数にし、暗号化する関数
    // 16進数 = 1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g でできている文字列
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    // sessionの情報を削除
    // unset() == ()内に指定した変数・配列を削除する
    unset($_SESSION);

    // thanks.phpに遷移する
    header('Location: thanks.php');

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
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <!--
      designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！
     -->

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
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <form method="post" action="" class="form-horizontal" role="form">
          <input type="hidden" name="action" value="submit">
          <div class="well">ご登録内容をご確認ください。</div>
            <table class="table table-striped table-condensed">
              <tbody>
                <!-- 登録内容を表示 -->
                <tr>
                  <td><div class="text-center">ニックネーム</div></td>
                  <td><div class="text-center"><?php echo $nickname; ?></div></td>
                </tr>
                <tr>
                  <td><div class="text-center">メールアドレス</div></td>
                  <td><div class="text-center"><?php echo $email; ?></div></td>
                </tr>
                <tr>
                  <td><div class="text-center">パスワード</div></td>
                  <td><div class="text-center"><?php echo $password; ?></div></td>
                </tr>
                <tr>
                  <td><div class="text-center">プロフィール画像</div></td>
                  <td><div class="text-center"><img src="../picture_path/<?php echo $picture_path; ?>" width="100" height="100"></div></td>
                </tr>
              </tbody>
            </table>

            <a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> |
            <input type="submit" class="btn btn-default" value="会員登録">
          </div>
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
  </body>
</html>
