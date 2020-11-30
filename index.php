<?php
 require('function.php');
 
 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('トップページ');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();
 
 //=======================================
 // 画面処理
 //=======================================
 //画面表示用データ

 debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理終了');
?>

<?php
 $siteTitle = 'HOME';
 require('head.php');
?>

<body>


<?php
 require('header.php');
?>

<!-- メインコンテンツ -->
<main>
    <div class="l-container l-container--lg">
            <h1 class="p-front-title c-front-title">WEBで勤怠管理。</h1>
            <div class="p-front-block">
                <p class="p-front-text c-front-text">「Work Schedule」は、Webで勤務時間の登録・編集など 日々の、勤怠管理が行えます。<p>
                <form class="p-front-btn__area" action="" method="post">
                    <?php if(empty($_SESSION['user_id'])): ?>
                        <div class="p-front-btn__parts">
                            <button type="button" onclick="location.href='signup.php'" class="p-btn c-btn c-btn--shadow">新規登録</button>
                        </div>     
                        <div class="p-front-btn__parts">
                            <button type="button" onclick="location.href='login.php'" class="p-btn c-btn c-btn--shadow">ログイン</button>
                        </div>
                    <?php else: ?> 
                        <div class="p-front-btn__parts">
                            <button type="button" onclick="location.href='logout.php'" class="p-btn c-btn c-btn--shadow">ログアウト</button>
                        </div>  
                    <?php endif; ?>     
                </form>
            </div>
    </div>

    
</main>


<?php
 require('footer.php');
?>

