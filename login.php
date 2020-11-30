<?php
 require('function.php');
 //ログイン認証
 require('auth.php');

 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('ログインページ');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();

 //=======================================
 // 画面処理
 //=======================================
 //画面表示用データ

 if(!empty($_POST)) {
     //変数にログイン情報を代入
     $email = $_POST['email'];
     $pass = $_POST['pass'];
     $pass_re = $_POST['pass_re'];
     $pass_save = (!empty($_POST['pass_save'])) ? true : false;

     //未入力チェック
     validRequired($email, 'email');
     validRequired($pass, 'pass');
     validRequired($pass_re, 'pass_re');

     if(empty($err_msg)) {
        //email形式チェック
        validEmail($email, 'email');
        //email最大文字数チェック
        validMaxLen($email, 'email');
        //パスワードの半角英数字チェック
        validHalf($pass, 'pass');
        //パスワードの最大文字数チェック
        validMaxLen($pass, 'pass');
        //パスワードの最小文字数チェック
        validMinlen($pass, 'pass');
        //パスワード同値チェック
        validMatch($pass, $pass_re, 'pass');

        if(empty($err_msg)) {
            debug('バリエーションチェックOKです');
            //例外処理
            try {
                $dbh = dbConnect();
                $sql = 'SELECT password,id FROM users WHERE email = :email';
                $data = array(':email' => $email);
                $stmt = queryPost($dbh, $sql, $data);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if(!empty($result) && password_verify($pass, array_shift($result))) {
                    debug('パスワード一致');

                    //ユーザーIDを格納
                    $_SESSION['user_id'] = $result['id'];
                    //ログイン有効期限（デフォルト1時間設定）
                    $sesLimit = 60*60;
                    //ログイン日時（現在日時に設定）
                    $_SESSION['login_date'] = time();
                    //ログイン保持あり
                    if(!empty($pass_save)) {
                        $_SESSION['login_limit'] = $sesLimit*24*30;
                    }else{
                        //ログイン保持なし
                        $_SESSION['login_limit'] = $sesLimit;
                    }
                    debug('マイページに遷移します');
                    header("Location:mypage.php");
                    exit();;
                }else{
                    debug('パスワードが一致しません');
                    $err_msg['common'] = MSG09;//メールアドレスまたはパスワードが違います
                }
            }catch(Exception $e){
                error_log('エラー発生：'.$e->getMessage());
                $error_msg['common'] = MSG08;//エラー発生、しばらく経ってからやり直してください
            }
        }
     }
 }

 debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理終了');
?>

<?php
 $siteTitle = 'ログイン';
 require('head.php');
?>

<body>

<?php
 require('header.php');
?>

<!-- メインコンテンツ -->
<main>
    <div class="l-container">
            <form action="" method="post" class="p-form" novalidate="novalidate">
                <h2 class="p-form-title">ログイン</h2>

               <!-- 共通メッセージ欄 -->
               <div class="c-form__area-msg">
                    <?php echo getErrMsg('common'); ?>
                </div>

                <!-- Email欄 -->
                <label class="p-form-label c-form-label <?php if(!empty($err_msg['email'])) echo 'err' ?>">
                    Email
                    <input type="email" name="email" value="<?php if(!empty($email)) echo $email; ?>" class="c-form__input">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('email'); ?>
                </div>

                <!-- パスワード欄 -->
                <label class="p-form-label c-form-label <?php if(!empty($err_msg['pass'])) echo 'err' ?>">
                    パスワード<span class="u-supp-text">※英数字6文字以上</span>
                    <input type="password" name="pass" value="<?php if(!empty($pass)) echo $pass; ?>" class="c-form__input">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('pass'); ?>
                </div>

                <!-- パスワード再入力欄 -->
                <label class="p-form-label c-form-label <?php if(!empty($err_msg['pass_re'])) echo 'err' ?>">
                    パスワード（再入力）
                    <input type="password" name="pass_re" value="<?php if(!empty($pass_re)) echo $pass_re; ?>" class="c-form__input">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('pass_re'); ?>
                </div>

                <!-- ログイン保持チェックボックス -->
                <label>
                    <input type="checkbox" name="pass_save" class="c-form__checkbox">次回ログインを省略する
                </label>

                <div class="p-btn-container">
                    <input type="submit" class="p-btn c-btn c-btn--shadow" value="ログイン">
                </div>
            </form>
    </div>
</main>

<?php
 require('footer.php');
?>
