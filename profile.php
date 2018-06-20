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
		// LEFT JOIN `繋げたい優先テーブル名` ON `繋げるテーブル`.`繋げるカラム`=`繋げたいテーブル`.`繋げたいカラム`
		$data = array($_GET['tweet_id']);
		$stmt = $dbh->prepare($sql);
    	$stmt->execute($data);

    	// テーブル結合なし
    	// $member_id = $stmt->fetch(PDO::FETCH_ASSOC);

    	// $member_sql = 'SELECT * FROM `members` WHERE `member_id`=?';
    	// $member_data = array($member_id['member_id']);
    	// $member_stmt = $dbh->prepare($member_sql);
    	// $member_stmt->execute($member_data);

    	$profile = $stmt->fetch(PDO::FETCH_ASSOC);
    	echo '<pre>';
    	var_dump($profile);
    	echo '</pre>';
	}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	<p>nickname</p>
	<p><?php echo $profile['nickname']; ?></p>
	<p>email</p>
	<p><?php echo $profile['email']; ?></p>
	<p>image</p>
	<img src="picture_path/<?php echo $profile['picture_path']; ?>" alt="">
</body>
</html>