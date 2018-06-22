<?php
	session_start();
	require('db_connect.php');

	// ログインチェック
	if (isset($_SESSION['login_id']) && $_SESSION['time'] + 3600 > time()) {
	    // $_SESSION['time']の時間を更新
	    $_SESSION['time'] = time();
	} else {
		// ログインしていない時
	    header('Location: login.php');
	}

	// 返信ツイートの作成
	if (!empty($_POST)) {
		if ($_POST['tweet'] == '') {
			$error['tweet'] = 'blank';
		}
		if (!isset($error)) {
			$sql = 'INSERT INTO `tweets` SET `tweet`=?, `member_id`=?, `reply_tweet_id`=?, `created`=NOW()';
			// $_POST['tweet'] = 返信内容、 $_SESSION['login_id'] = 返信する人、 $_GET['tweet_id'] = 返信先のID
			$data = array($_POST['tweet'], $_SESSION['login_id'], $_GET['tweet_id']);
			$stmt = $dbh->prepare($sql);
		    $stmt->execute($data);

		    header('Location: index.php');
		}
	}

	// 返信する投稿の内容を取得するSQL
	$sql = 'SELECT * FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweet_id`=?';
	$data = array($_GET['tweet_id']);
	$stmt = $dbh->prepare($sql);
	$stmt->execute($data);
	$tweet = $stmt->fetch(PDO::FETCH_ASSOC);

	// 返信先のツイートとツイートのユーザーを代入
	$reply_msg = "@".$tweet['tweet']."(".$tweet['nickname'].")";

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
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <h4><?php echo $tweet['tweet']; ?>へ返信</h4>
        <div class="msg">
          <form method="post" action="" class="form-horizontal" role="form">
              <!-- つぶやき -->
              <div class="form-group">
                <label class="col-sm-4 control-label">返信内容</label>
                <div class="col-sm-8">
                  <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo $reply_msg.' : '; ?></textarea>
                  <?php if (isset($error)): ?>
                  	<p class="error">* 入力されていません。</p>
                  <?php endif ?>
                </div>
              </div>
            <ul class="paging">
              <input type="submit" class="btn btn-warning" value="返信する">
            </ul>
          </form>
        </div>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>