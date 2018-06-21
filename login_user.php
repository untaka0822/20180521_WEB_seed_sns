<?php
  session_start();
  require('db_connect.php');

  if (!isset($_SESSION['login_id'])) {
  	header('Location: login.php');
  } else {
  	$sql = 'SELECT `members`.*, `tweets`.`tweet` FROM `members` LEFT JOIN `tweets` ON `members`.`member_id`=`tweets`.`member_id` WHERE `members`.`member_id`=?';
  	$data = array($_GET['member_id']);
  	$stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $login = $stmt->fetch(PDO::FETCH_ASSOC);

    $login_tweet = array();
    while(true) {
    	$login['tweet'] = $stmt->fetch(PDO::FETCH_ASSOC);
    	if ($login['tweet'] == false) {
    		break;
    	}
    	$login_tweet[] = $login['tweet'];
    }
  }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	<?php if (!empty($_GET)): ?>
		<p>ニックネーム : </p>
		<p><?php echo $login['nickname']; ?></p>
		<p><?php echo $login['email']; ?></p>
		<p>全てのコメント</p>
		<?php foreach ($login_tweet as $tweet): ?>
			<p><?php echo $tweet['tweet']; ?></p>
		<?php endforeach ?>
		<p><img src="picture_path/<?php echo $login['picture_path']; ?>" alt=""></p>
	<?php endif ?>
</body>
</html>