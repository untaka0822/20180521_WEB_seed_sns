<?php
	session_start();
  require('db_connect.php');

	if (!isset($_SESSION['login_id'])) {
  		header('Location: login.php');
  } else {
  	$sql = 'SELECT * FROM `members` WHERE `member_id`=?';
  	$data = array($_SESSION['login_id']);
  	$stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $login = $stmt->fetch(PDO::FETCH_ASSOC);

    $follower_sql = 'SELECT * FROM `follows` LEFT JOIN `members` ON `follows`.`member_id`=`members`.`member_id` WHERE `follows`.`follower_id`=?';
    $follower_data = array($_SESSION['login_id']);
    $follower_stmt = $dbh->prepare($follower_sql);
    $follower_stmt->execute($follower_data);
    $follows = array();
    while(true) {
      $follow = $follower_stmt->fetch(PDO::FETCH_ASSOC);
      if ($follow == false) {
        break;
      }
      $follows[] = $follow;
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
        <img src="picture_path/<?php echo $login['picture_path']; ?>" width="250" height="200">
        <h3><?php echo $login['nickname']; ?></h3>
        <h4><?php echo $login['email']; ?></h4>
        <br>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
      <div class="col-md-9 content-margin-top">
          <div class="msg_header">
			<a href="follower.php">フォロワー</a> / <a href="following.php">フォロー</a>
          </div>
          <div class="msg">
            <p>名前 : ああああ</p>
            <p>メールアドレス : ああああ</p>
            <p class="day">
              フォローしてもらった日
            </p>
          </div>
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
