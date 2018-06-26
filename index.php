<?php
  session_start();
  require('db_connect.php');

  // ログインチェック
  // 前回ログインした時間より1時間以上経ってる時にアクセスしたらlogin.phpに遷移する
  // 1時間 == 3600秒
  if (isset($_SESSION['login_id']) && $_SESSION['time'] + 3600 > time()) {

    // $_SESSION['time']の時間を更新
    $_SESSION['time'] = time();

    // ログインしている時
    $sql = 'SELECT * FROM `members` WHERE `member_id`=?';
    $data = array($_SESSION['login_id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    // ログインしているユーザーのデータが入っている
    $login_member = $stmt->fetch(PDO::FETCH_ASSOC);
  } else {
    // ログインしていない時
    header('Location: login.php');
  }

  // つぶやくボタンが押された時
  if (!empty($_POST)) {

    // 入力チェック
    if ($_POST['tweet'] == '') {
      $error['tweet'] = 'blank';
    }

    if (!isset($error)) {
      // sql文作成 INSERT INTO
      // tweet = つぶやいた内容
      // member_id = ログインしているユーザーのid
      // reply_tweet_id = -1
      // created = datetime型 = NOW()使用
      // modified = phpMyAdmin側 = 書く必要ない

      $sql = 'INSERT INTO `tweets` SET `tweet`=?, `member_id`=?, `reply_tweet_id`=-1, `created`=NOW()';
      $data = array($_POST['tweet'], $_SESSION['login_id']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
    }
  }

  // ページング処理
  // ツイートを見やすくするため、5ツイート1ページにする機能を追加
  // 空の変数を用意
  // $page = '';

  // パラメータが存在していたら$pageにページ番号を代入する
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
  } else {
    // 存在していない時はデフォルト値を1にする
    $page = 1;
  }

  // もし1以下のイレギュラーな数字が入ってきた時、ページ番号を強制的に1にする
  // max() = カンマ区切りで並んでいる数字の中から最大の数字を取得
  $page = max($page, 1);

  // 1ページ分の表示件数を設定
  $max_page_tweet = 5;

  // ツイートの件数から最大のページ数を計算する
  // ツイートの件数を取得
  $page_sql = 'SELECT COUNT(*) AS `count` FROM `tweets`';
  $page_stmt = $dbh->prepare($page_sql);
  $page_stmt->execute();
  $max_tweets = $page_stmt->fetch(PDO::FETCH_ASSOC);

  // 小数点の切り上げ
  // ページの最大数
  $all_pages_number = ceil($max_tweets['count'] / $max_page_tweet);

  // パラメータの数字に最大ページを超えた数字を入れられた場合に強制的に最後のページとする
  // min() = カンマ区切りで並んでいる数字の中から最小の数字を取得
  $page = min($page, $all_pages_number);

  // 表示するデータの取得開始場所
  $start_page = ($page - 1) * $max_page_tweet;

  // 一覧用のつぶやき全件を最新順に取得
  $tweet_sql = "SELECT `tweets`.*, `members`.`nickname`, `members`.`picture_path`, `members`.`created` AS `member_created` FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `delete_flag`=0 ORDER BY `tweets`.`created` DESC LIMIT ".$start_page.",".$max_page_tweet;
  // SELECT 取得したいカラム FROM 取得したいテーブル LEFT JOIN 繋げたいテーブル ON 取得したいテーブル.繋げるキー=繋げたいテーブル.繋げるキー ORDER BY 順番 昇順か降順か LIMIT 開始位置,取得する個数
  // LIMIT == 取得するデータの制限をする
  // LIMIT 開始位置, 取得する個数
  $tweet_stmt = $dbh->prepare($tweet_sql);
  $tweet_stmt->execute();

  // 空の配列を作ることでデータがない時のエラーを防ぐ
  $tweets = array();

  // 全件分fetchしている
  while (true) {
    $tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);
    if ($tweet == false) {
      break;
    }
    // ツイートにログインユーザーがいいねしているかどうかのフラグ作成
    // ①like数を求めるSQL文
    $like_sql = 'SELECT COUNT(*) AS `like_count` FROM `likes` WHERE `tweet_id`=?';
    // ②SQL文実行、フェッチして値を取得
    // ③一行分のデータに新しいキーを用意して、like数を代入


    // ④ログインしている人がLikeしているかどうかの情報を取得
    
    // ⑤SQL文実行
    // ⑥フェッチして取得
    // ⑦tweetのキーに上記で行った値を入れる

    $tweets[] = $tweet;
  }

  // echo '<br>';
  // echo '<pre>';
  // var_dump($tweets);
  // echo '</pre>';
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
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<a href="login_user.php?member_id=<?php echo $_SESSION['login_id']; ?>"><?php echo $login_member['nickname']; ?></a>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
                <?php if (isset($error)): ?>
                  <p class="error">* 何も入力されていません。</p>
                <?php endif ?>
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if ($page > 1): ?>
                  <li><a href="index.php?page=<?php echo $page - 1 ?>" class="btn btn-default">前</a></li>
                <?php else: ?>
                  <li>前</li>
                <?php endif ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if ($page < $all_pages_number): ?>
                  <li><a href="index.php?page=<?php echo $page + 1 ?>" class="btn btn-default">次</a></li>
                <?php else: ?>
                 <li>次</li>
                <?php endif ?>
                 <li><?php echo $page; ?> / <?php echo $all_pages_number; ?> Page</li>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
        <?php foreach ($tweets as $tweet) { ?>
        <div class="msg">
          <img src="picture_path/<?php echo $tweet['picture_path']; ?>" width="48" height="48">
          <p>
            <?php echo $tweet['tweet']; ?><span class="name"><a href="profile.php?member_id=<?php echo $tweet['member_id']; ?>">(<?php echo $tweet['nickname']; ?>) </a></span>
            [<a href="reply.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">Re</a>]
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">
              <?php echo $tweet['created']; ?>
            </a>
            <?php if ($tweet['member_id'] == $login_member['member_id']): ?>
              [<a href="edit.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #00994C;">編集</a>]
              [<a href="delete.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #F33;">削除</a>]
            <?php endif ?>
            [<a href="like.php?like_tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: blue;"><i class="fa fa-thumbs-o-up">いいね</i></a>]
            [<a href="like.php?dislike_tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: blue;"><i class="fa fa-thumbs-o-down">よくないね</i></a>]
          </p>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
