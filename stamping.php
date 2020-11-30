<?php
 require('function.php');
 //ログイン認証
 require('auth.php');

 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('打刻ページ');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();

 //=======================================
 // 画面処理
 //=======================================

 //年月日の定義
 $apply = array( "日", "月", "火", "水", "木", "金", "土" );
 $todayApply = $apply[date("w")];
 $today = date('Y-m-d');//DB登録用変数
 $ym = date('Y-m月');//DB登録用変数
 $today_ymd = date('Y年m月d日');//表示用変数

 debug('ym?:'.print_r($ym, true));

 //今日の打刻情報があるかどうか
 $dbFormData = (!empty($_SESSION['user_id']) && !empty($today)) ? getTodayStamping($_SESSION['user_id'], $today) : ''; 
 //修正ボタンから飛んできた場合、st_idを格納
 $st_id = (!empty($_GET['st_id'])) ? $_GET['st_id'] : '';
 //DBからst_idと一致する打刻データを取得
 $dbChoseStampingData = (!empty($st_id)) ? getChoseStamping($_SESSION['user_id'], $st_id) : ''; 
 //新規登録か修正か判別用
 $edit_flg = (empty($dbFormData) && empty($st_id)) ? false : true;

 //画面表示用データ
 if(!empty($_POST)) {
    $week = date("w");
    $sort_id = $_POST['sort_id'];
    $start = $_POST['start'];
    $closed = $_POST['closed'];
    $break_id = $_POST['break_id'];

    //今日の打刻情報がない、修正ボタンも押されていない場合
    if(empty($dbChoseStampingData) && empty($dbFormData)) {
        //未入力チェック
        validRequired($start, 'start');
        validRequired($closed, 'closed');
        //数字桁数チェック
        validDigit_4($start, 'start');
        validDigit_4($closed, 'closed');
    //修正ボタンが押された場合
    }elseif(!empty($dbChoseStampingData) && empty($dbFormData)){
        if($dbChoseStampingData['start'] !== $start) {
            validRequired($start, 'start');
            validDigit_4($start, 'start');
        }
        if($dbChoseStampingData['closed'] !== $closed) {
            validRequired($closed, 'closed');
            validDigit_4($closed, 'closed');
        }
    //今日の打刻情報がある場合
    }elseif(empty($dbChoseStampingData) && !empty($dbFormData)){
        if($dbFormData['start'] !== $start) {
            validRequired($start, 'start');
            validDigit_4($start, 'start');
        }
        if($dbFormData['closed'] !== $closed) {
            validRequired($closed, 'closed');
            validDigit_4($closed, 'closed');
        }
    }

    if(empty($err_msg)) {
        debug('バリエーションチェックOKです。');

        try {
            $dbh = dbConnect();
            if($edit_flg) {
                debug('更新です。');
                $sql = 'UPDATE stamping SET sort_id = :sort_id, start = :start, closed = :closed, break_id = :break_id WHERE user_id = :user_id AND id = :st_id';
                $data = array(':user_id' => $_SESSION['user_id'], ':sort_id' => $sort_id, ':start' => $start, ':closed' => $closed, ':break_id' => $break_id, ':st_id' => $st_id);
            }else {
                debug('新規登録です。');
                $sql = 'INSERT INTO stamping (user_id, sort_id, today, ym, week, start, closed, break_id, create_date) 
                                     VALUES (:user_id, :sort_id, :today, :ym, :week, :start, :closed, :break_id, :date)';
                $data = array(':user_id' => $_SESSION['user_id'], ':sort_id' => $sort_id, ':today' => $today, ':ym' => $ym, ':week' => $week, ':start' => $start, ':closed' => $closed, ':break_id' => $break_id, ':date' => date('Y-m-d H:i:s'));
            }
                $stmt = queryPost($dbh, $sql, $data);

            //クエリ成功の場合
            if($stmt && $edit_flg) {
                $_SESSION['msg_success'] = SUC02;//更新しました
                debug('マイページに遷移します');
                header("Location:mypage.php");
                exit();
            }elseif($stmt && !$edit_flg) {
                $_SESSION['msg_success'] = SUC01;//登録しました
                debug('マイページに遷移します');
                header("Location:mypage.php");
                exit();
            }
        }catch(Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
            $error_msg['common'] = MSG08;
        }
    }    
 }
 

 debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理終了');
?>

<?php
 $siteTitle = '打刻ページ';
 require('head.php');
?>

<body>

<?php
 require('header.php');
?>

<!-- メインコンテンツ -->
<main>
    <div class="l-container">

<!-- 修正ボタンが押された場合         -->
<?php if(!empty($dbChoseStampingData)): ?>

        <h2 class="p-form-title">
            <!-- 年月日 -->
            <?php echo $dbChoseStampingData['today']; ?>
            <!-- 曜日 -->
            (<?php echo $dbChoseStampingData['week']; ?>)       
        </h2>

        <!-- 共通メッセージ欄 -->
        <div class="c-form__area-msg">
            <?php echo getErrMsg('common'); ?>
        </div>
            
            <form action="" method="post" class="p-form" novalidate="novalidate">
                <!-- 区分 -->
                <div class="p-form c-form__cat">
                    区 分:
                    <select name="sort_id" class="c-select-box p-select-box--md">                 
                        <option value="1" <?php if(getFormData('sort_id') == 1) echo 'selected'; ?>>通 常</option>
                        <option value="2" <?php if(getFormData('sort_id') == 2) echo 'selected'; ?>>代 休</option>
                        <option value="3" <?php if(getFormData('sort_id') == 3) echo 'selected'; ?>>有 給</option>
                        <option value="4" <?php if(getFormData('sort_id') == 4) echo 'selected'; ?>>半 勤</option>
                    </select>
                </div>

                <!-- 出勤 -->
                <label class="p-form-label c-form-label c-form-label--lg <?php if(!empty($err_msg['start'])) echo 'err' ?>">
                    出 勤:<span class="u-supp-text">（例）8時30分の場合は「０８３０」</span>
                    <input type="number" name="start" value="<?php echo getFormData('start'); ?>" class="c-form__input c-form__input--sm">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('start'); ?>
                </div>

                <!-- 退勤 -->
                <label class="p-form-label c-form-label c-form-label--lg <?php if(!empty($err_msg['closed'])) echo 'err' ?>">
                    退 勤:<span class="u-supp-text">（例）19時30分の場合は「１９３０」</span>
                    <input type="number" name="closed" value="<?php echo getFormData('closed'); ?>" class="c-form__input c-form__input--sm">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('closed'); ?>
                </div>

                <!-- 休憩 -->             
                <div class="p-form c-form__cat">
                    休　憩:
                    <select name="break_id" class="c-select-box p-select-box--sm">
                        <option value="1" <?php if(getFormData('break_id') == 1) echo 'selected'; ?>>0</option>
                        <option value="2" <?php if(getFormData('break_id') == 2) echo 'selected'; ?>>45</option>
                        <option value="3" <?php if(getFormData('break_id') == 3) echo 'selected'; ?>>60</option>
                    </select>
                    分
                </div>

<!-- まだ今日の打刻していない、修正ボタンも押されていない場合 -->
<?php elseif(empty($dbChoseStampingData) && empty($dbFormData)): ?>
    <h2 class="p-form-title">
            <!-- 年月日 -->
            <?php echo $today; ?>
            <!-- 曜日 -->
            (<?php echo $todayApply; ?>)       
        </h2>

        <!-- 共通メッセージ欄 -->
        <div class="c-form__area-msg">
            <?php echo getErrMsg('common'); ?>
        </div>
            
            <form action="" method="post" class="p-form" novalidate="novalidate">
                <!-- 区分 -->
                <div class="p-form c-form__cat">
                    区 分:
                    <select name="sort_id" class="c-select-box p-select-box--md">                 
                        <option value="1" <?php if(getFormData('sort_id') == 1) echo 'selected'; ?>>通 常</option>
                        <option value="2" <?php if(getFormData('sort_id') == 2) echo 'selected'; ?>>代 休</option>
                        <option value="3" <?php if(getFormData('sort_id') == 3) echo 'selected'; ?>>有 給</option>
                        <option value="4" <?php if(getFormData('sort_id') == 4) echo 'selected'; ?>>半 勤</option>
                    </select>
                </div>

                <!-- 出勤 -->
                <label class="p-form-label c-form-label c-form-label--lg <?php if(!empty($err_msg['start'])) echo 'err' ?>">
                    出 勤:<span class="u-supp-text">（例）8時30分の場合は「０８３０」</span>
                    <input type="number" name="start" value="<?php echo getFormData('start'); ?>" class="c-form__input c-form__input--sm">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('start'); ?>
                </div>

                <!-- 退勤 -->
                <label class="p-form-label c-form-label c-form-label--lg <?php if(!empty($err_msg['closed'])) echo 'err' ?>">
                    退 勤:<span class="u-supp-text">（例）19時30分の場合は「１９３０」</span>
                    <input type="number" name="closed" value="<?php echo getFormData('closed'); ?>" class="c-form__input c-form__input--sm">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('closed'); ?>
                </div>

                <!-- 休憩 -->             
                <div class="p-form c-form__cat">
                    休　憩:
                    <select name="break_id" class="c-select-box p-select-box--sm">
                        <option value="1" <?php if(getFormData('break_id') == 1) echo 'selected'; ?>>0</option>
                        <option value="2" <?php if(getFormData('break_id') == 2) echo 'selected'; ?>>45</option>
                        <option value="3" <?php if(getFormData('break_id') == 3) echo 'selected'; ?>>60</option>
                    </select>
                    分
                </div>

<!-- 今日は打刻済み、修正ボタンは押されていない場合 -->
<?php elseif(empty($dbChoseStampingData) && !empty($dbFormData)): ?>
    <h2 class="p-form-title">
            <!-- 年月日 -->
            <?php echo $dbFormData['today']; ?>
            <!-- 曜日 -->
            (<?php echo $dbFormData['week']; ?>)       
        </h2>

        <!-- 共通メッセージ欄 -->
        <div class="c-form__area-msg">
            <?php echo getErrMsg('common'); ?>
        </div>
            
            <form action="" method="post" class="p-form" novalidate="novalidate">
                <!-- 区分 -->
                <div class="p-form c-form__cat">
                    区 分:
                    <select name="sort_id" class="c-select-box p-select-box--md">
                        <option value="1" <?php if(getFormData('sort_id') == 1) echo 'selected'; ?>>通 常</option>
                        <option value="2" <?php if(getFormData('sort_id') == 2) echo 'selected'; ?>>代 休</option>
                        <option value="3" <?php if(getFormData('sort_id') == 3) echo 'selected'; ?>>有 給</option>
                        <option value="4" <?php if(getFormData('sort_id') == 4) echo 'selected'; ?>>半 勤</option>
                    </select>
                </div>

                <!-- 出勤 -->
                <label class="p-form-label c-form-label c-form-label--lg <?php if(!empty($err_msg['start'])) echo 'err' ?>">
                    出 勤:<span class="u-supp-text">（例）8時30分の場合は「０８３０」</span>
                    <input type="number" name="start" value="<?php echo getFormData('start'); ?>" class="c-form__input c-form__input--sm">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('start'); ?>
                </div>

                <!-- 退勤 -->
                <label class="p-form-label c-form-label c-form-label--lg <?php if(!empty($err_msg['closed'])) echo 'err' ?>">
                    退 勤:<span class="u-supp-text">（例）19時30分の場合は「１９３０」</span>
                    <input type="number" name="closed" value="<?php echo getFormData('closed'); ?>" class="c-form__input c-form__input--sm">
                </label>
                <div class="c-form__area-msg">
                    <?php echo getErrMsg('closed'); ?>
                </div>

                <!-- 休憩 -->             
                <div class="p-form c-form__cat">
                    休　憩:
                    <select name="break_id" class="c-select-box p-select-box--sm">
                        <option value="1" <?php if(getFormData('break_id') == 1) echo 'selected'; ?>>0</option>
                        <option value="2" <?php if(getFormData('break_id') == 2) echo 'selected'; ?>>45</option>
                        <option value="3" <?php if(getFormData('break_id') == 3) echo 'selected'; ?>>60</option>
                    </select>
                    分
                </div>
<?php endif; ?>               
                <div class="p-btn-container">
                    <input type="submit" class="p-btn c-btn c-btn--shadow" value="<?php echo (!$edit_flg) ? '登録する' : '更新する' ?>">
                </div>     
            </form>

            <p class="p-form-foot">【補足】<br>
                ・区分[通常]以外の出勤,退勤,休憩時間は定時時間を入力。<br>
                ・出勤,退勤時間は5分単位で入力。</p>
        </div>
</main>

<?php
 require('footer.php');
?>
