<?php
	session_start();
	require('db_connect.php');

	// ログインチェック
	if (!isset($_SESSION['login_id'])) {
		header('Location: index.php');
	}

	// GET送信されている場合
	if (!empty($_GET)) {
		// テーブル結合 LEFT JOIN
		// 複数のテーブルがあり、それを結合する際、優先テーブルを1つ決めて、そこにある情報を全て優先的に取得する
		$sql = 'SELECT * FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweet_id`=?';
		// LEFT JOIN `繋げたいテーブル名` ON `繋げる優先テーブル`.`繋げるカラム`=`繋げたいテーブル`.`繋げたいカラム`
		$data = array($_GET['tweet_id']);
		$stmt = $dbh->prepare($sql);
    	$stmt->execute($data);

    	$tweets = array();
    	while (true) {
    		$tweet = $stmt->fetch(PDO::FETCH_ASSOC);
    		if ($tweet == false) {
    			break;
    		}
    		$tweets[] = $tweet;
    	}

    	echo '<br>';
    	echo '<br>';
    	echo '<pre>';
    	var_dump($tweets);
    	echo '</pre>';
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
        <img src="http://placehold.it/200x200">
        <h3><?php echo $profile['nickname']; ?></h3>
        <a href="profile.php"><button class="btn btn-block btn-default">フォロー</button></a>
        <a href="profile.php"><button class="btn btn-block btn-default">フォロー解除</button></a>
        <br>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
      <div class="col-md-9 content-margin-top">
        <div class="msg_header">
        <a href="follow.php">Followers<span class="badge badge-pill badge-default"></span></a>
        <a href="following.php">Followings<span class="badge badge-pill badge-default"></span></a>
        </div>
        <div class="msg">
          <img src="http://placehold.it/50x50">
          <p>つぶやき : <br></p>
          <p class="day">
          </p>
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
