<?php
 require('function.php');
 //ログイン認証
//  require('auth.php');

 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('マイページ');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();

 //=======================================
 // 画面処理
 //=======================================
 $email = $_POST['email'];
 $user = $_SESSION['login_date'];

 debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理終了');
?>

<?php
 $siteTitle = 'マイページ';
 require('head.php');
?>

<body>

<?php
 require('header.php');
?>



<!-- メインコンテンツ -->
<main>
    <div class="l-container">         
        <h2 class="p-form-title">勤怠情報</h2>
<p>email:<?php echo $email; ?></p>
<p>user:<?php echo $user; ?></p>
        
    </div>

</main>

<?php
 require('footer.php');
?>