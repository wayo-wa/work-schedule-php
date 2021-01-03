<?php
 require('function.php');
 //ログイン認証
 require('auth.php');

 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('マイページ');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();

 //=======================================
 // 画面処理
 //=======================================
 //ユーザーID
 $u_id = $_SESSION['user_id'];
 //DBからユーザー基本情報を取得する
 $dbUserData = getUsers($u_id);
 //DB登録用変数
 $today = date('Y-m-d');
 //DB登録用変数
 $ym = date('Y-m月');
 //検索年月の選択状況
 $ym_id = (!empty($_POST['ym_id'])) ? $_POST['ym_id'] : '';
 //DBからユーザーの当月の打刻データを取得する
 $dbMyStampingData = getMyStamping($u_id, $ym);
 //今日の打刻情報があるかどうか
 $dbFormData = getTodayStamping($u_id, $today); 
 //検索用の年月を取得
 $dbMonthlyDate = getMonthlyDate($u_id); 
 //検索年月と一致する打刻情報
 $dbYmData = getYmData($u_id, $ym_id);

 
 debug('年月と一致する打刻情報を取得：'.print_r($dbYmData, true));
 debug('当月の打刻情報:'.print_r($dbMyStampingData, true));
 debug('検索用年月は:'.print_r($dbMonthlyDate, true));

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

<!-- セッションメッセージの表示 -->
<p id="js-show-msg" class="js-slide-msg">
    <?php echo getSessionFlash('msg_success'); ?>
</p>

<!-- メインコンテンツ -->
<main>
    <div class="l-container">         
        <h2 class="p-form-title">勤怠情報</h2>

        <div class="p-form-header">
            <div class="p-form-header__name c-form-header__name">
                <p>【従業員名】<?php if(!empty($dbUserData)) echo $dbUserData['username']; ?><p>
            </div>
            <!-- 年月検索 -->
            <form action="" method="post" class="u-mg-8">
                <select name="ym_id" class="c-select-box p-select-box--lg">
                    <option value="0 <?php if(getFormData('ym_id') == 0){echo 'selected';} ?>">年月検索</option>
                    <?php foreach($dbMonthlyDate as $key => $val) { ?>
                        <option value="<?php echo $val['ym']; ?>" <?php if(getFormData('ym_id') == $val['ym']){echo 'selected';} ?>>
                        <?php echo $val['ym']; ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="submit" class="c-btn__search" value="検索">
            </form> 
        </div>
            
        <form action="" method="get" class="u-mgt-24">
         <div class="p-table__scroll">
            <table class="c-table">
                <thead>
                    <tr>
                        <th class="c-table__item">日 付</th>
                        <th class="c-table__item">出 勤</th>
                        <th class="c-table__item">退 勤</th>
                        <th class="c-table__item">休 憩</th>
                        <th class="c-table__item">区 分</th>
                    </tr>
                </thead>
                
                <!-- 当月の打刻情報 -->
                <?php if(!empty($dbMyStampingData) && empty($dbYmData)): ?>       
                <tbody>
                    <?php
                     if(!empty($dbMyStampingData)):
                        foreach($dbMyStampingData as $key => $val):
                    ?>
                    <tr>
                        <th class="c-table__item"><?php echo $val['today'];; ?></th>
                        <td class="c-table__item"><?php echo $val['start']; ?></td>
                        <td class="c-table__item"><?php echo $val['closed']; ?></td>
                        <td class="c-table__item"><?php echo $val['break']; ?></td>
                        <td class="c-table__item"><?php echo $val['sort']; ?></td>
                        <td class="c-table__item"><a href="stamping.php<?php echo '?st_id='.$val['id']; ?>" class="p-btn c-btn c-btn--modify">修正</a></td>
                        <td class="c-table__item">
                            <!-- data属性に値を与えて、jsのajaxでajaxDelete.phpへ渡して、削除処理を行う -->
                            <input class="p-btn c-btn c-btn--modify js-button-click" data-stampingid="<?php echo $val['id']; ?>" type="submit" value="削除">
                        </td>
                    </tr>
                    <?php
                     endforeach;
                    endif;
                    ?>
                </tbody>
            </table>

            <!-- 年月検索された場合 -->
            <?php elseif(!empty($dbYmData)): ?>
                <tbody>
                    <?php
                     if(!empty($dbYmData)):
                        foreach($dbYmData as $key => $val):
                    ?>
                    <tr>
                        <th class="c-table__item"><?php echo $val['today'];; ?></th>
                        <td class="c-table__item"><?php echo $val['start']; ?></td>
                        <td class="c-table__item"><?php echo $val['closed']; ?></td>
                        <td class="c-table__item"><?php echo $val['break']; ?></td>
                        <td class="c-table__item"><?php echo $val['sort']; ?></td>
                        <td class="c-table__item"><a href="stamping.php<?php echo '?st_id='.$val['id']; ?>" class="p-btn c-btn c-btn--modify">修正</a></td>
                        <td class="c-table__item">
                            <input class="p-btn c-btn c-btn--modify js-button-click" data-stampingid="<?php echo $val['id']; ?>" type="submit" value="削除">
                        </td>
                    </tr>
                    <?php
                     endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
         </div>

        <!-- まだ情報がない場合 -->
        <?php else: ?>
                <p>勤怠情報はありません</p>
        <?php endif; ?>       

        </form>
    </div>

</main>

<?php
 require('footer.php');
?>