<?php
session_start();
require('dbconnect.php');

// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';

if(!isset($_SESSION['LearnSNSp1']['id'])){
  header('Location: signin.php');
}




$sql = 'SELECT * FROM `users` WHERE `id`= ?';
$data = [$_SESSION['LearnSNSp1']['id']];
$stmt = $dbh->prepare($sql);
$stmt->execute($data);

$signin_user = $stmt->fetch(PDO::FETCH_ASSOC);

// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';

$errors = [];

if(!empty($_POST)){
  $feed = $_POST['feed']; 

  // echo '<pre>';
  // var_dump($feed);
  // echo '</pre>';
  // die();

  if($feed != ''){
    // バリデーション通過時
    $sql = 'INSERT INTO `feeds`(`feed`, `user_id`, `created`) VALUES(?,?,NOW()) ';
    $data = [$feed,$signin_user['id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);


    header('Location: timeline.php');
    exit();




    }else{
        $errors['feed'] = 'blank';
    }
}



const CONTENT_PER_PAGE = 5;

if(isset($_GET['page'])){
    $page = $_GET['page'];
}else{
    $page = 1;
}

// echo '<pre>';
// var_dump($page);
// echo '</pre>';

// -1など不正な値を渡された時の対策
$page = max($page,1);

// ヒットしたレコードの件数を取得するSQL
$sql_count = 'SELECT COUNT(*) AS `cnt` FROM `feeds` ';
$stmt_count = $dbh->prepare($sql_count);
$stmt_count->execute();

$record_cnt = $stmt_count->fetch(PDO::FETCH_ASSOC);

// 最後のページから何ページになるのかを算出
// 最後のページ＝取得したページ数÷1ページあたりに表示する件数
$last_page = ceil($record_cnt['cnt']/CONTENT_PER_PAGE);

// 最後のページよりも大きい数を渡された時の対策
$page = min($page,$last_page);

$start = ($page-1)*CONTENT_PER_PAGE;



$sql = 'SELECT `f`.*, `u`.`name`, `u`.`img_name` FROM `feeds` AS `f` LEFT JOIN `users` AS `u` ON `f`.`user_id` = `u`.`id` WHERE `user_id` ORDER BY `f`.`created` DESC LIMIT ' . CONTENT_PER_PAGE . ' OFFSET '.$start;

$stmt = $dbh->prepare($sql);
$stmt->execute();






$feeds = [];
while (true) {
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if($record == false){
        break;
    }
    $feeds[] = $record;
}






?>
<?php include('layouts/header.php'); ?>
<body style="margin-top: 60px; background: #E4E6EB;">
    <?php include('navbar.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-3">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="timeline.php?feed_select=news">新着順</a></li>
                    <li><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
                </ul>
            </div>
            <div class="col-xs-9">
                <div class="feed_form thumbnail">
                    <form method="POST" action="">
                        <div class="form-group">
                            <textarea name="feed" class="form-control" rows="3" placeholder="Happy Hacking!" style="font-size: 24px;"></textarea><br>
                        <?php if(isset($errors['feed']) && $errors['feed'] == 'blank'): ?>
                          <p class="text-danger">投稿内容を入力してください</p>
                        <?php endif; ?>
                        </div>
                        <input type="submit" value="投稿する" class="btn btn-primary">
                    </form>
                </div>
                <?php foreach($feeds as $feed): ?>
                    <div class="thumbnail">
                        <div class="row">
                            <div class="col-xs-1">
                                <img src="user_profile_img/<?php echo $feed['img_name']; ?>" width="40px">
                            </div>
                            <div class="col-xs-11">
                                <a href="profile.php" style="color: #7f7f7f;"><?php echo $feed['name']; ?></a>
                                <?php echo $feed['created']; ?>
                            </div>
                        </div>
                        <div class="row feed_content">
                            <div class="col-xs-12">
                                <span style="font-size: 24px;"><?php echo $feed['feed']; ?></span>
                            </div>
                        </div>
                        <div class="row feed_sub">
                            <div class="col-xs-12">
                                <button class="btn btn-default">いいね！</button>
                                いいね数：
                                <span class="like-count">10</span>
                                <a href="#collapseComment" data-toggle="collapse" aria-expanded="false"><span>コメントする</span></a>
                                <span class="comment-count">コメント数：5</span>
                                <?php if($feed['user_id'] == $signin_user['id']): ?>
                                    <a href="edit.php?feed_id=<?php echo $feed['id']; ?>" class="btn btn-success btn-xs">編集</a>
                                    <a onclick="return confirm('ほんとに消すの？');" href="delete.php?feed_id=<?php echo $feed['id']; ?>" class="btn btn-danger btn-xs">削除</a>
                                <?php endif; ?>
                            </div>
                            <?php include('comment_view.php'); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div aria-label="Page navigation">
                    <ul class="pager">
                        <!-- Newer 押せない時 1より前は押せないようにする -->
                        <?php if($page == 1): ?>
                            <li class="previous disabled"><a><span aria-hidden="true">&larr;</span> Newer</a></li>
                        <!-- Newer 押せる時 -->
                        <?php else : ?>
                            <li class="previous"><a href="timeline.php?page=<?php echo $page -1; ?>"><span aria-hidden="true">&larr;</span> Newer</a></li>
                        <?php endif ; ?>


                        <!-- Older 押せない時 最後のページより前は押せないようにする -->
                        <?php if($page == $last_page ): ?>
                            <li class="next disabled"><a>Older <span aria-hidden="true">&rarr;</span></a></li>
                        <!-- Older 押せる時 -->
                        <?php else : ?>
                            <li class="next"><a href="timeline.php?page=<?php echo $page +1; ?>">Older <span aria-hidden="true">&rarr;</span></a></li>
                        <?php endif ; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('layouts/footer.php'); ?>
</html>
