<?php
session_start();
require('dbconnect.php');


$errors = [];

if(!empty($_POST)){
    $email = $_POST['input_email'];
    $password = $_POST['input_password'];

    if($email !='' && $password !=''){
        // データベースとの照合
        $sql = 'SELECT * FROM `users` WHERE `email`=? ';
        $data = [$email];
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);


        // echo '<pre>';
        // var_dump($record);
        // echo '</pre>';



        // メールアドレスでの本人確認

        if($record == false){
            $errors['signin'] = 'failed';
        }

        if(password_verify($password,$record['password'])){
            // 認証成功
            $_SESSION['LearnSNSp1']['id'] = $record['id'];

            // echo '<pre>';
            // var_dump($_SESSION);
            // echo '</pre>';

            header('Location: timeline.php');
            exit();

        }else{
            // 認証失敗
            $errors['signin'] = 'blank';
        }
    }
}



echo '<pre>';
var_dump($_POST);
echo '</pre>';
echo '<pre>';
var_dump($errors);
echo '</pre>';

?>
<?php include('layouts/header.php'); ?>
<body style="margin-top: 60px">
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2 thumbnail">
                <h2 class="text-center content_header">サインイン</h2>
                <form method="POST" action="signin.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="email">メールアドレス</label>
                        <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com">
                    <?php if(isset($errors['signin']) && $errors['signin'] == 'blank'): ?>
                      <p class="text-danger">メールアドレスとパスワードを正しく入力してください</p>
                    <?php endif; ?>
                    <?php if(isset($errors['signin']) && $errors['signin'] == 'failed'): ?>
                      <p class="text-danger">サインインに失敗しました</p>
                    <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="password">パスワード</label>
                        <input type="password" name="input_password" class="form-control" id="password" placeholder="4 ~ 16文字のパスワード">
                    </div>
                    <input type="submit" class="btn btn-info" value="サインイン">
                    <span style="float: right; padding-top: 6px;">
                        <a href="index.php">戻る</a>
                    </span>
                </form>
            </div>
        </div>
    </div>
</body>
<?php include('layouts/header.php'); ?>
</html>
