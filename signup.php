<?php
 require('function.php');
 //ログイン認証
 require('auth.php');

 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('新規登録ページ');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();

 //=======================================
 // 画面処理
 //=======================================
 //画面表示用データ
 if(!empty($_POST)){

    //変数にユーザー情報を代入
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //未入力チェック
    validRequired($name, 'name');
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if(empty($err_msg)){
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
            validEmailDump($email);
            //例外処理
            try {
                //DB接続
                $dbh = dbConnect();
                $sql = 'INSERT INTO users(username, email, password, login_time, create_date)
                        VALUES(:name, :email, :pass, :login_time, :create_date)';
                $data = array(':name' => $name,
                              ':email' => $email,
                              ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                              ':login_time' => date('Y-m-d H:i:s'),
                              ':create_date' => date('Y-m-d H:i:s'));
                $stmt = queryPost($dbh, $sql, $data);

                //クエリ成功の場合の処理
                if($stmt) {
                    //ログイン有効期限（デフォルト1時間とする）
                    $sesLimit = 60*60;
                    //最終ログイン日時を現在日時に
                    $_SESSION['login_date'] = time();
                    $_SESSION['login_limit'] = $sesLimit;
                    //ユーザーIDを格納
                    $_SESSION['user_id'] = $dbh -> lastInsertId();

                    debug('セッション変数の中身：'.print_r($_SESSION,true));
                    debug('マイページに遷移します');

                    header("Location:mypage.php");
                    exit();
                }
            }catch(Exception $e) {
                error_log('エラー発生：'.$e->getMessage());
                $error_msg['common'] = MSG08;//エラーが発生しました
            }
        }      
    }    
 }

 debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理終了');
?>

<?php
 $siteTitle = '新規登録';
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
                <h2 class="p-form-title">新規登録</h2>

                <!-- 共通メッセージ欄 -->
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('common'); ?>
                </div>

                <!-- 氏名欄 -->
                <label class="p-form-label c-form-label <?php if(!empty($err_msg['name'])) echo 'err'; ?>">
                    氏   名
                    <input type="text" name="name" value="<?php if(!empty($name)) echo $name; ?>" class="c-form__input c-form__input">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('name'); ?>
                </div>

                <!-- Email欄 -->
                <label class="p-form-label c-form-label <?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                    Email
                    <input type="email" name="email" value="<?php if(!empty($email)) echo $email; ?>" class="c-form__input c-form__input">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('email'); ?>
                </div>

                <!-- パスワード欄 -->
                <label class="p-form-label c-form-label <?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                    パスワード<span class="u-supp-text">※英数字6文字以上</span>
                    <input type="password" name="pass" value="<?php if(!empty($pass)) echo $pass; ?>" class="c-form__input c-form__input">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('pass'); ?>
                </div>

                <!-- パスワード再入力欄 -->
                <label class="p-form-label c-form-label <?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                    パスワード（再入力）
                    <input type="password" name="pass_re" value="<?php if(!empty($pass_re)) echo $pass_re; ?>" class="c-form__input c-form__input">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('pass_re'); ?>
                </div>

                <div class="p-btn-container">
                    <input type="submit" class="p-btn c-btn c-btn--shadow" value="登録する">
                </div>     
            </form>
        </div>
</main>

<?php
 require('footer.php');
?>