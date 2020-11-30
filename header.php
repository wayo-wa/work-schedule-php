<header class="l-header">
    <div class="l-header__inner">
        <div class="p-header__block">
            <h1 class="c-header__title"><a href="index.php" class="c-header__title--link">Work Schedule</a></h1>
            <nav class="js-header__nav">
                <ul class="p-nav__block">
                    <?php if(empty($_SESSION['user_id'])): ?>
                        <li class="c-nav__list"><a href="signup.php" class="p-nav__link c-nav__link">新規登録</a></li>
                        <li class="c-nav__list"><a href="login.php" class="p-nav__link c-nav__link">ログイン</a></li>
                    <?php else: ?>
                            <li class="c-nav__list"><a href="stamping.php" class="p-nav__link c-nav__link">本日打刻</a></li>
                            <li class="c-nav__list"><a href="mypage.php" class="p-nav__link c-nav__link">マイページ</a></li>
                            <li class="c-nav__list"><a href="logout.php" class="p-nav__link c-nav__link">ログアウト</a></li> 
                    <?php endif; ?>
                </ul>
            </nav>
            <!-- ハンバーガーメニュー -->
            <div class="js-toggle-sp-menu js-menu-trigger">
                <span class="js-menu-trigger__parts"></span>
                <span class="js-menu-trigger__parts"></span>
                <span class="js-menu-trigger__parts"></span>
            </div>
        </div>
    </div>
</header>