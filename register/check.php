<?php
session_start();
require('../dbconnect.php');

if(!isset($_SESSION['LearnSNSp1'])){
    header('Location: signup.php');
    exit();
}

// echo '<pre>';
// var_dump($_SESSION['LearnSNSp1']);
// echo '</pre>';

$name = $_SESSION['LearnSNSp1']['name'];
$email = $_SESSION['LearnSNSp1']['email'];
$password = $_SESSION['LearnSNSp1']['password'];
$img_name = $_SESSION['LearnSNSp1']['img_name'];


if(!empty($_POST)){
    // echo '通過！';
    $sql = 'INSERT INTO `users`(`name`, `email`, `password`, `img_name`, `created`) VALUES (?,?,?,?,NOW())';
    $data = [$name,$email,password_hash($password,PASSWORD_DEFAULT),$img_name];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    unset($_SESSION['LearnSNSp1']);
    header('Location: thanks.php');
    exit();

}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Learn SNS</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
</head>
<body style="margin-top: 60px">
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2 thumbnail">
                <h2 class="text-center content_header">アカウント情報確認</h2>
                <div class="row">
                    <div class="col-xs-4">
                        <img src="../user_profile_img/<?php echo htmlspecialchars($img_name); ?>" class="img-responsive img-thumbnail">
                    </div>
                    <div class="col-xs-8">
                        <div>
                            <span>ユーザー名</span>
                            <p class="lead"><?php echo htmlspecialchars($name); ?></p>
                        </div>
                        <div>
                            <span>メールアドレス</span>
                            <p class="lead"><?php echo htmlspecialchars($email); ?></p>
                        </div>
                        <div>
                            <span>パスワード</span>
                            <p class="lead">●●●●●●●●</p>
                        </div>
                        <form method="POST" action="check.php">
                            <a href="signup.php?action=rewrite" class="btn btn-default">&laquo;&nbsp;戻る</a> | 
                            <input type="hidden" name="action" value="submit">
                            <input type="submit" class="btn btn-primary" value="ユーザー登録">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>