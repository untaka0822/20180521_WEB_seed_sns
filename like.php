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

	// いいね!ボタンを押した時
	if (isset($_GET['like_tweet_id'])) {
		$sql = 'INSERT INTO `likes` SET `tweet_id`=?, `member_id`=?';
		$data = array($_GET['like_tweet_id'], $_SESSION['login_id']);
		$stmt = $dbh->prepare($sql);
    	$stmt->execute($data);

    	header('Location: index.php');
	}

	// 良くないねボタンを押した時
	if (isset($_GET['dislike_tweet_id'])) {
		$sql = 'DELETE FROM `likes` WHERE `tweet_id`=? AND `member_id`=?';
		$data = array($_GET['dislike_tweet_id'], $_SESSION['login_id']);
		$stmt = $dbh->prepare($sql);
    	$stmt->execute($data);

    	header('Location: index.php');
	}

?>