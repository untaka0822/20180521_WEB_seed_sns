<?php
  // $_SESSIONを使用するために必要
  session_start();

  // 違う階層にあるため
  require('../db_connect.php');

  echo '<br>';
  echo '<br>';
  // var_dump($_FILES);

  // check.phpからの書き直し処理
  if (!empty($_GET) && $_GET['action'] == 'rewrite') {
    // 書き直すために初期表示する値を変数に格納する
    $nickname = $_SESSION['join']['nickname'];
    $email = $_SESSION['join']['email'];
    $password = $_SESSION['join']['password'];
  } else {
    // 通常の初期表示
    $nickname = '';
    $email = '';
    $password = '';
  }

  // POST送信された時入力チェック
  if (!empty($_POST)) {
    // nicknameが空だった時
    if ($_POST['nickname'] == '') {
      $error['nickname'] = 'blank';
    }

    // emailが空だった時
    if ($_POST['email'] == '') {
      $error['email'] = 'blank';
    }

    // passwordが空だった時
    if ($_POST['password'] == '') {
      $error['password'] = 'blank';
    } elseif (mb_strlen($_POST['password']) < 4) {
      // strlen() = 文字の長さ(バイト数)を数字で返してくれる関数
      // mb_strlen() = 文字の長さ(文字数)を数字で返してくれる関数
      // $_POST['password']が4文字以上の時
      $error['password'] = 'length';
    }

    // isset()は値がセットされていればtrue、いなければfalseを返す
    // !は意味が逆になる
    // 例) !isset() はセットされていなければtrue、いればfalseを返す
    // 下の文は入力チェック後に$error配列に値がセットされていなければ処理実行
    if (!isset($error)) {
      // emailの重複チェック
      // DBに同じemailのデータがあるかチェックする
      // なぜ？
      // メールアドレスが重複していた場合、メールでの通知やSELECT文での取得の際に重複してしまう可能性があるため

      // Formタグ内に書いたメールアドレスと被らないような処理を書く必要がある
      $sql = 'SELECT COUNT(email) AS `count` FROM `members` WHERE `email`=?';
      // AS = カラムのキーを任意の文字に変えられる
      $data = array($_POST['email']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

      // 重複しているかどうかの結果を取得する
      $email_count = $stmt->fetch(PDO::FETCH_ASSOC);
      // var_dump($email_count);

      // もし$email_count['count']が1以上の時
      if ($email_count['count'] >= 1) {
        $error['email'] = 'duplicated';
      }

      // 上の$error['email']の値がない時 == 重複していない時
      // エラーがなければ中の処理を行う
      if (!isset($error)) {
        // 画像の拡張子チェック
        // jpg,png,gifの3つは登録できるようにする
        // substr == 文字列から範囲を指定した文字列を切り取る
        // substr(元になる文字列, 切り出す文字のスタート位置)
        // -(マイナス)の場合は末尾から数える
        // $_FILES['picture_path']['name'] == ファイル名
        $ext = substr($_FILES['picture_path']['name'], -3);

        if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif') {
          // 画像のアップロード処理
          // 例) hayato.pngを指定した時、$picture_pathの中身を20180614143358hayato.png
          // 重複を防ぐためにdate関数をファイル名にくっつけ日付を与える
          // date() == 日付を取得する関数
          // date('フォーマット', フォーマットを当てたい値)
          // Y = Year, m = month, d = day, H = Hour, i = minutes, s = seconds
          // 2018-6-20 = date('Y-m-d')
          // 2018/06/24 = date('Y/m/d')
          $picture_name = date('YmdHis') . $_FILES['picture_path']['name'];
          // ファイルをアップロード
          // move_uploaded_file(アップロードしたいファイル,サーバのどこにどういう名前でアップロードするか指定)
          move_uploaded_file($_FILES['picture_path']['tmp_name'], '../picture_path/' . $picture_name);

          // $_SESSION
          // $_SESSIONは値をセッションに保存してどのページからでも取得できるようにする
          // $_POSTの値をSESSIONに保存する必要がある
          // 条件 : session_start()を使用すること
          $_SESSION['join'] = $_POST;
          $_SESSION['join']['picture_path'] = $picture_name;
          // var_dump($_SESSION);

          header('Location: check.php');

        } else {
          $error['picture_path'] = 'type';
        }
      }
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
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>会員登録</legend>
        <!-- enctype="multipart/form-data" -->
        <!-- ファイルの送信時にformタグ内に記載する必要がある -->
        <!-- 記載しなければPOST送信では、画像の名前しか送られない -->
        <form method="POST" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
          <!-- ニックネーム -->
          <div class="form-group">
            <label class="col-sm-4 control-label">ニックネーム</label>
            <div class="col-sm-8">
              <input type="text" name="nickname" class="form-control" placeholder="例： Seed kun" value="<?php echo $nickname; ?>">
              <?php if (isset($error['nickname'])) { ?>
                <p class="error">* ニックネームを入力してください</p>
              <?php } ?>
            </div>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com" value="<?php echo $email; ?>">
              <?php if (isset($error['email']) && $error['email'] == 'blank') { ?>
                <p class="error">* メールアドレスを入力してください</p>
              <?php } elseif (isset($error['email']) && $error['email'] == 'duplicated') { ?>
                <p class="error">* 入力されたメールアドレスは登録済みです</p>
              <?php } ?>
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
              <?php if (isset($error['password']) && $error['password'] == 'blank') { ?>
                <p class="error">* パスワードを入力してください</p>
              <?php } elseif(isset($error['password']) && $error['password'] == 'length') { ?>
                <p class="error">パスワードは4文字以上入力してください</p>
              <?php } ?>
            </div>
          </div>
          <!-- プロフィール写真 -->
          <div class="form-group">
            <label class="col-sm-4 control-label">プロフィール写真</label>
            <div class="col-sm-8">
              <input type="file" name="picture_path" class="form-control">
              <?php if (isset($error['picture_path']) && $error['picture_path'] == 'type') { ?>
                <p class="error">* jpgまたはpngまたはgifのみ使用できます</p>
              <?php } ?>
            </div>
          </div>

          <input type="submit" class="btn btn-default" value="確認画面へ"> &nbsp;|&nbsp;
          <a href="../login.php" class="btn btn-default">ログイン</a>
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
