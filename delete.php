<?php
	session_start();
	require ('db_connect.php');

	// GET送信されてきた時
	if (!empty($_GET)) {
		$sql = 'DELETE FROM `tweets` WHERE `tweet_id`=?';
		$data = array($_GET['tweet_id']);
		$stmt = $dbh->prepare($sql);
     	$stmt->execute($data);

     	header('Location: index.php');
	}
?>