<?php
	session_start();

	// SESSIONの情報を削除(上書き)
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}

	// SESSIONの情報を削除
	// session_destroy == $_SESSION自体を削除
	// unset()は、配列の要素を削除している
	session_destroy();

	// COOKIEの情報を削除
	setcookie('email', '',time() -3000);
	setcookie('password', '',time() -3000);

	// ログイン後の画面に戻る
	header('Location: index.php');

	// ログイン後の画面に移動することでしっかりと消せているかどうか確認するために移動する
?>