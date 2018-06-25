<?php 
    
    session_start();
    require('db_connect.php');

    if (isset($_SESSION['login_id'])) {
         $sql = 'SELECT * FROM `members` LEFT JOIN `tweets` ON `members`.`member_id`=`tweets`.`member_id` WHERE `members`.`member_id`=? AND `reply_tweet_id`=-1 ORDER BY `tweets`.`created` DESC ';
         $data = array($_GET['member_id']);
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
    } else {
        header('Location: login.php');
    }

    $reply_sql = 'SELECT `tweets`.*, `members`.`nickname`, `members`.`email` FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweets`.`member_id`=? AND `reply_tweet_id`!=-1';
    $reply_data = array($_GET['member_id']);
    $reply_stmt = $dbh->prepare($reply_sql);
    $reply_stmt->execute($reply_data);
    $replys = array();
    while (true) {
        $reply = $reply_stmt->fetch(PDO::FETCH_ASSOC);
        if ($reply == false) {
            break;
        }
        $replys[] = $reply;
    }
    echo '<pre>';
    var_dump($replys);
    echo '</pre>';

 ?>

 <!DOCTYPE html>
 <html lang="ja">
 <head>
  <meta charset="utf-8">
   <title>プロフィール</title>
   <link href="assets/css/bootstrap.css" rel="stylesheet">
   <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
   <link href="assets/css/form.css" rel="stylesheet">
   <link href="assets/css/timeline.css" rel="stylesheet">
   <link href="assets/css/main.css" rel="stylesheet">
 </head>
 <body>
     
     <div class="container">
         <div class="row">
            <div class="form-group">
                <div class="col-md-2">
                  <img src="picture_path/<?php echo $tweets[0]['picture_path']; ?>" width="300px" height="300px">
                   <p>名前</p>
                   <p><?php echo $tweets[0]['nickname']; ?></p>
                   <p>メールアドレス</p>
                   <p><?php echo $tweets[0]['email']; ?></p>
                </div>
                <div class="col-md-1">
                </div>
                  <div class="form-group" >
                    <div class="col-md-6">
                        <p>全てのコメント (最新順)</p>
                        ------------------------------------------
                        <?php foreach ($tweets as $tweet) { ?>
                          <p><?php echo $tweet['tweet']; ?></p>
                          <p><?php echo $tweet['created']; ?></p>
                        ------------------------------------------
                        <?php } ?>

                    </div>
                  </div>
                    <!-- <div class="col-md-1">
                    </div> -->
                        <div class="col-md-3">
                          <p>返信ツイート</p>
                          ---------------------------------------
                          <?php foreach ($replys as $reply): ?>
                          <!-- <?php//if ($_GET['member_id'] == $reply['reply_tweet_id']) { ?>
                            <p><?php// echo $reply['tweet']; ?></p>
                          <?php// } ?> -->
                          <!-- <p><?php// echo $reply['nickname']; ?></p> -->
                          <p><?php echo $reply['tweet']; ?></p>
                          <p><?php echo $reply['created']; ?></p>
                          ---------------------------------------
                          <?php endforeach ?>
                          <a href="index.php">戻る</a>
                        </div>
            </div>
         </div>
     </div>
 </body>
 </html>