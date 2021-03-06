<?php
	session_start();
	require('db_connect.php');

	// ログインチェック
	if (!isset($_SESSION['login_id'])) {
		header('Location: index.php');
	}

  // フォローボタンが押された時
  if (!empty($_GET['following_id'])) {
    $sql = 'INSERT INTO `follows` SET `member_id`=?, `follower_id`=?';
    $data = array($_SESSION['login_id'], $_GET['following_id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    header('Location: profile.php?member_id='.$_GET['following_id']);
  }

  // フォロー解除ボタンが押された時
  if (!empty($_GET['unfollow_id'])) {
    $sql = 'DELETE FROM `follows` WHERE`member_id`=? AND `follower_id`=?';
    $data = array($_SESSION['login_id'], $_GET['unfollow_id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    header('Location: profile.php?member_id='.$_GET['unfollow_id']);
  }

	// GET送信されている場合
	if (!empty($_GET)) {
		// テーブル結合 LEFT JOIN
		// 複数のテーブルがあり、それを結合する際、優先テーブルを1つ決めて、そこにある情報を全て優先的に取得する
		$sql = 'SELECT * FROM `members` LEFT JOIN `tweets` ON `members`.`member_id`=`tweets`.`member_id` WHERE `members`.`member_id`=?';
		// LEFT JOIN `繋げたいテーブル名` ON `繋げる優先テーブル`.`繋げるカラム`=`繋げたいテーブル`.`繋げたいカラム`
		$data = array($_GET['member_id']);
		$stmt = $dbh->prepare($sql);
    $stmt->execute($data);

  	$tweets = array();
    $reply_tweets = array();

  	while (true) {
      $tweet = $stmt->fetch(PDO::FETCH_ASSOC);
  		if ($tweet == false) {
  			break;
  		}
  		$tweets[] = $tweet;
  	}

    for($i=0; $i < count($tweets); $i++) {
      $reply_sql = 'SELECT * FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweet_id`=?';
      $reply_data = array($tweets[$i]['reply_tweet_id']);
      $reply_stmt = $dbh->prepare($reply_sql);
      $reply_stmt->execute($reply_data);

      $reply_tweets = array();
      while (true) {
        $reply_tweet = $reply_stmt->fetch(PDO::FETCH_ASSOC);
        if ($reply_tweet == false) {
          break;
        }
        $reply_tweets[] = $reply_tweet;
      }
    }

    for($i=0; $i < count($tweets); $i++) {
      for ($n=0; $n < count($reply_tweets); $n++) {
        if ($tweets[$i]['reply_tweet_id'] == $reply_tweets[$n]['tweet_id']) {
          $tweets[$i]['reply_user_id'] = $reply_tweets[$n]['member_id'];
          $tweets[$i]['reply_user'] = $reply_tweets[$n]['nickname'];
          $tweets[$i]['reply_tweet'] = $reply_tweets[$n]['tweet'];
        }
      }
    }
  }

  // ログインしているユーザーがそのユーザーに対してフォローしているかどうかの判定
  $sql = 'SELECT COUNT(*) AS `count` FROM `follows` WHERE `member_id`=? AND `follower_id`=?';
  $data = array($_SESSION['login_id'], $_GET['member_id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  $follow_count = $stmt->fetch(PDO::FETCH_ASSOC);
  $follow = $follow_count['count'];

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
      <div class="col-md-3 content-margin-top">
        <img src="picture_path/<?php echo $tweets[0]['picture_path']; ?>" width="200" height="200">
        <h3><?php echo $tweets[0]['nickname']; ?></h3>
        <h4><?php echo $tweets[0]['email']; ?></h4>
        <!-- <a href="profile.php"><button class="btn btn-block btn-default">フォロー</button></a>
        <a href="profile.php"><button class="btn btn-block btn-default">フォロー解除</button></a> -->
        <br>
        <?php if (isset($follow) && $follow == 0): ?>
        <a href="profile.php?following_id=<?php echo $_GET['member_id']; ?>" class="btn btn-default" style="width: 100%;">フォロー</a>
        <?php else: ?>
        <a href="profile.php?unfollow_id=<?php echo $_GET['member_id']; ?>" class="btn btn-default" style="width: 100%;">フォロー解除</a>
        <?php endif ?>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
      <div class="col-md-9 content-margin-top">
          <div class="msg_header">
            このユーザーのツイート一覧
          <!-- <a href="follow.php">Followers<span class="badge badge-pill badge-default"></span></a>
          <a href="following.php">Followings<span class="badge badge-pill badge-default"></span></a> -->
          </div>
          <?php foreach ($tweets as $tweet): ?>
            <div class="msg">
              <p>つぶやき :<br> <?php echo $tweet['tweet']; ?></p>
              <p class="day">
                <?php echo $tweet['created']; ?>
              </p>
              <?php if ($tweet['reply_tweet_id'] != -1): ?>
                <p>返信先 : <a href="view.php?tweet_id=<?php echo $tweet['reply_tweet_id']; ?>"><?php echo $tweet['reply_tweet']; ?></a></p>
                <p>返信相手 : <a href="profile.php?member_id=<?php echo $tweet['reply_user_id']; ?>"><?php echo $tweet['reply_user']; ?></a></p>
              <?php endif ?>
            </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
