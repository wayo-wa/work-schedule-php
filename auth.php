<?php
 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('ログイン認証');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();

if(!empty($_SESSION['login_date'])) {
    debug('ログイン済みユーザー');
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()) {
        debug('有効期限切れ');
        session_destroy();
        header("Location:login.php");
        exit(); 
    }else{
        debug('有効期限内');
        $_SESSION['login_date'] = time();
        if(basename($_SERVER['PHP_SELF']) === 'login.php' || basename($_SERVER['PHP_SELF']) === 'signup.php') {
            header("Location:mypage.php");
            exit();
        }
    }
}else{
    debug('未ログインユーザー');
    if(basename($_SERVER['PHP_SELF']) === 'mypage.php' || basename($_SERVER['PHP_SELF']) === 'stamping.php'){
        header("Location:index.php");
        exit();
    }
}

