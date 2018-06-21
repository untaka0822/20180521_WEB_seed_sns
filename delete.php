<?php
	session_start();
	require ('db_connect.php');

	// GET送信されてきた時
	if (!empty($_GET)) {
		// 論理削除
		$sql = 'UPDATE `tweets` SET `delete_flag`=? WHERE `tweet_id`=?';
		$data = array(1, $_GET['tweet_id']);
		$stmt = $dbh->prepare($sql);
     	$stmt->execute($data);

     	header('Location: index.php');
	}
?>