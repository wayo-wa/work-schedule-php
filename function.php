<?php
 //=================================
 // ログ
 //=================================
 //ログを取る
 ini_set('log_errors', 'on');
 //ログの出力ファイル
 ini_set('error_log', 'php.log');

 //=================================
 // デバッグ
 //=================================
 //デバッグフラグ
 $debug_flg = true;
 //デバッグログ関数
 function debug($str) {
    global $debug_flg;
    //  if(!empty($debug_flg)) { //※開発中 
     if(empty($debug_flg)) { 
         error_log('デバッグ：'.$str);
     }
 }

 //=================================
 // セッション準備・セッション有効期限を延ばす
 //=================================
 //セッションファイルの置き場所
 session_save_path("C:/xampp/tmp");
 //ガーページコレクションが削除するセッションの有効期限の設定
 ini_set('session.gc_maxlifetime', 60*60*24*30);
 //ブラウザを閉じてもクッキー自体の有効期限を伸ばす
 ini_set('session_cookie_lifetime', 60*60*24*30);
 //セッションを使う
 session_start();
 //現在のセッションIDを新しく生成したものと置き換える
 session_regenerate_id();

 //=================================
 // 画面表示処理開始ログ吐き出し関数
 //=================================
 function debugLogStart(){
     debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
     debug('セッションID:'.session_id());
     debug('セッション変数の中身:'.print_r($_SESSION, true));
     debug('現在日時:'.time());
     if(!empty($_SESSION['login_data']) && !empty($_SESSION['login_limit'])) {
         debug('ログイン期限日時:'.($_SESSION['login_data'] + $_SESSION['login_limit']));
     }
 }

 //=================================
 // グローバル変数
 //=================================
 $err_msg = array();

 //=================================
 // 定数
 //=================================
 define('MSG01', '入力必須です');
 define('MSG02', 'Email形式で入力して下さい');
 define('MSG03', '255文字以内で入力して下さい');
 define('MSG04', '6文字以上で入力して下さい');
 define('MSG05', '半角英数字で入力して下さい');
 define('MSG06', 'パスワード(再入力)が一致しません');
 define('MSG07', 'このEmailは既に登録されています');
 define('MSG08', 'エラーが発生しました。しばらく経ってからやり直してください。');
 define('MSG09', 'メールアドレスまたはパスワードが違います。');
 define('MSG10', '4桁の数字で入力して下さい。');
 define('MSG11', '休憩時間が正しくありません。');

 define('SUC01', '登録しました。');
 define('SUC02', '更新しました。');
 define('SUC03', '削除しました。');

 //=================================
 // バリデーション関数
 //=================================
 //バリデーション（未入力チェック）
 function validRequired($str, $key) {
     if($str === '') {
         global $err_msg;
         $err_msg[$key] = MSG01; //入力必須です
     }
 }

 //バリデーション（Email形式チェック）
 function validEmail($str, $key) {
     if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) {
         global $err_msg;
         $err_msg[$key] = MSG02; //Email形式で入力して下さい
     }
 }

 //バリデーション（最大文字数チェック）
 function validMaxLen($str, $key, $max = 255) {
     if(mb_strlen($str) > $max) {
            global $err_msg;
            $err_msg[$key] = MSG03; //255文字以内で入力して下さい
     }
 }

 //バリデーション（最小文字数チェック）
 function validMinLen($str, $key, $min = 6) {
     if(mb_strlen($str) < $min) {
         global $err_msg;
         $err_msg[$key] = MSG04; //6文字以上で入力して下さい
     }
 }

 //バリデーション（半角英数字チェック）
 function validHalf($str, $key) {
     if(!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
         global $err_msg;
         $err_msg[$key] = MSG05; //半角英数字で入力して下さい
     }
 }

 //バリデーション（4桁の数字）
 function validDigit_4($str, $key) {
     if(!preg_match("/^[0-9]{4}+$/",$str)) {
         global $err_msg;
         $err_msg[$key] = MSG10;
     }
 }

 //バリデーション（パスワード同値チェック）
 function validMatch($str1, $str2, $key) {
     if($str1 !== $str2) {
         global $err_msg;
         $err_msg[$key] = MSG06; //パスワード（再入力）が一致しません
     }
 }

 //バリデーション（Email重複チェック）
 function validEmailDump($email) {
     global $err_msg;
     //例外処理
     try {
         $dbh = dbConnect();
         $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
         $data = array(':email' => $email);
         $stmt = queryPost($dbh, $sql, $data);
         $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!empty(array_shift($result))) {
                $err_msg['email'] = MSG07;//このEmailは既に登録されています
            }
        }catch(Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG08;//エラーが発生しました。しばらく経ってからやり直してください。
        } 
 }
 
 //エラーメッセージの表示
 function getErrMsg($key) {
     global $err_msg;
     if(!empty($err_msg[$key])) {
         return $err_msg[$key];
     }
 }

 //=================================
 // データベース関連
 //=================================
 function dbConnect() {
     //DB接続情報を読み込む
    //  require_once __DIR__ . '/../../db_info/require.php';
    //  $db = dbSetting();

    //local用
    //  $dbh = 'mysql:dbname=work_schedule;host=localhost;charset=utf8';

    //heroku用
     $dbh = 'mysql:dbname=heroku_8f6dc3fa8d8721b;host=us-cdbr-east-02.cleardb.com;charset=utf8';

     $user = 'b247c32b10dde8';
     $password = '2cafb2d5';
     //db_infoから読み込む
    //  $user = $db['db_user'];
    //  $password = $db['db_pass'];

     $option = array(
         PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
     );
     //PDOオブジェクト生成（DBへ接続）
     $dbh = new PDO($dbh, $user, $password, $option);
     return $dbh;
 }
 

 //クエリ作成関数
 function queryPost($dbh, $sql, $data) {
     $stmt = $dbh->prepare($sql);
     if(!$stmt->execute($data)){
         debug('クエリに失敗しました');
         debug('sqlエラー:'.print_r($stmt->errorInfo(),true));
         global $err_msg;
         $err_msg['common'] = MSG08;
         return 0;
     }
     debug('クエリ成功');
     return $stmt;
 }
 //=================================
 // サニタイズ関数
 //=================================
 function sanitize($str) {
     return htmlspecialchars($str, ENT_QUOTES);
 }
 
 //=================================
 // フォームの入力チェック及び保持
 //=================================
 function getFormData($str, $flg = false) {
     if($flg) {
         $method = $_GET;
     }else{
         $method = $_POST;
     }
     global $dbFormData;//今日の打刻情報
     global $dbChoseStampingData;//修正ボタンが押された打刻情報
     global $dbYmData;//検索年月と一致する打刻情報
     global $err_msg;

     debug('$dbFormData:'.print_r($dbFormData, true));
     debug('$dbChoseStampingData:'.print_r($dbChoseStampingData, true));
     debug('$dbYmData:'.print_r($dbYmData, true));

     //DBデータと比較する必要がない場合
     if(isset($dbYmData) && empty($dbFomeData) && empty($dbChoseStampingData)) {
        if(isset($method[$str])) {
            return sanitize($method[$str]);
        }
     //$dbFormDataの入力チェック
     }elseif(!empty($dbFormData) && empty($dbChoseStampingData)) {
         //フォームにエラーがある場合
         if(!empty($err_msg)) {
             //POSTあり
             if(isset($method['str'])) {
                 return sanitize($method[$str]);
             //POSTなし（基本ありえない）
             }else{
                 return sanitize($dbFormData[$str]);
             }
         //フォームにエラーがない場合
         }else{
            //POSTデータとDBデータが違う場合
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]) {
                return sanitize($method[$str]);
            //POSTデータとDBデータが同じ場合
            }else{
                return sanitize($dbFormData[$str]);
            }
        }
    //$dbChoseStampingDataの入力チェック
    }elseif(!empty($dbChoseStampingData)) {
        //フォームにエラーがある場合
        if(!empty($err_msg)) {
            //POSTあり
            if(isset($method['str'])) {
                return sanitize($method[$str]);
            //POSTなし（基本ありえない）
            }else{
                return sanitize($dbChoseStampingData[$str]);
            }
        //フォームにエラーがない場合
        }else{
           //POSTデータとDBデータが違う場合
           if(isset($method[$str]) && $method[$str] !== $dbChoseStampingData[$str]) {
               return sanitize($method[$str]);
           //POSTデータとDBデータが同じ場合
           }else{
               return sanitize($dbChoseStampingData[$str]);
            }
        }
    } 
 }
 
 //=================================
 // ユーザー情報の取得
 //=================================
 function getUsers($u_id) {
     debug('ユーザー基本情報を取得します。');
     try {
        $dbh =dbConnect();
        $sql = 'SELECT id,username,email,password FROM users WHERE id = :u_id';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else {
            return false;
        }
     }catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
     }
 }
 //=================================
 // 修正ボタンが押された打刻情報の取得
 //=================================
 function getChoseStamping($u_id, $st_id) {
     debug('修正ボタンが押された打刻情報を取得します。');
     try {
        $dbh = dbConnect();
        $sql = 'SELECT
                    st.id,
                    st.sort_id, 
                    st.today, 
                    st.start, 
                    st.closed, 
                    st.break_id,
                    a.name AS week
                FROM stamping AS st LEFT JOIN sort AS so
                ON st.sort_id = so.id
                INNER JOIN users AS u
                ON st.user_id = u.id
                INNER JOIN apply AS a
                ON st.week = a.apply_id
                WHERE  st.user_id = :u_id AND st.id = :st_id';
        $data = array(':u_id' => $u_id, ':st_id' => $st_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
     }catch(Exception $e) {
         error_log('エラー発生：'.$e->getMessage());
     }
 }
 
 //=================================
 // ユーザーの当月の打刻情報の取得
 //=================================
 function getMyStamping($u_id, $ym) {
    debug('ユーザーの当月の打刻情報を取得します。');
    debug('u_id?:'.print_r($u_id, true));
    debug('ym?:'.print_r($ym, true));
    try {
        $dbh = dbConnect();
        $sql = 'SELECT
                    st.id, st.today, st.ym, st.start, st.closed,
                    so.name AS sort,
                    u.id AS user,
                    a.name AS week,
                    b.name AS break
                FROM stamping AS st LEFT JOIN sort AS so
                ON st.sort_id = so.id
                INNER JOIN users AS u
                ON st.user_id = u.id
                INNER JOIN apply AS a
                ON st.week = a.apply_id
                INNER JOIN break AS b
                ON st.break_id = b.id
                WHERE st.user_id = :u_id AND st.ym = :ym ORDER BY st.create_date';
        $data = array(':u_id' => $u_id, ':ym' => $ym);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt) {
            return $stmt->fetchAll();
        }else{
            return false;
        }

    }catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
 }

 //=================================
 // 検索用年月別リストを作成
 //=================================
 function getMonthlyDate($u_id) {
     debug('検索用の年月を取得します');
     debug('u_idは：'.print_r($u_id, true));
     try {
        $dbh = dbConnect();
        $sql = 'SELECT ym FROM stamping WHERE user_id = :u_id GROUP BY ym ORDER BY create_date';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt) {
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
 }
 
 //=================================
 // 年月から一致する打刻情報を取得する
 //=================================
 function getYmData($u_id, $ym) {
     debug('年月と一致する打刻情報を取得します。');
     debug('u_id?:'.print_r($u_id, true));
     debug('ym?:'.print_r($ym, true));
     try {
        $dbh = dbConnect();
        $sql = 'SELECT
                st.id, st.today, st.ym, st.start, st.closed,
                so.name AS sort,
                u.id AS user,
                a.name AS week,
                b.name AS break
                FROM stamping AS st LEFT JOIN sort AS so
                ON st.sort_id = so.id
                INNER JOIN users AS u
                ON st.user_id = u.id
                INNER JOIN apply AS a
                ON st.week = a.apply_id
                INNER JOIN break AS b
                ON st.break_id = b.id
                WHERE user_id = :u_id AND ym = :ym ORDER BY st.create_date';
        $data = array(':u_id' => $u_id, ':ym' => $ym);
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt) {
            return $stmt->fetchAll();
        }
     }catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
     }
 }


 //=================================
 // 今日の打刻情報の取得
 //=================================
 function getTodayStamping($u_id, $today) {
     debug('u_id:'.print_r($u_id,true));
     debug('today:'.print_r($today,true));
     try {
         $dbh = dbConnect();
         $sql = 'SELECT
                    st.id,st.sort_id, st.today, st.start, st.closed, st.break_id,
                    a.name AS week
                FROM stamping AS st LEFT JOIN apply AS a
                ON st.week = a.apply_id
                WHERE user_id = :u_id AND today = :today';
        $data = array(':u_id' => $u_id, ':today' => $today);
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e) {
       
         error_log('エラー発生：'.$e->getMessage());
     }
 }

 //=================================
 // セッションを一回だけ取得する
 //=================================
 function getSessionFlash($key) {
     if(!empty($_SESSION[$key])) {
         $data = $_SESSION[$key];
         $_SESSION[$key] = '';
         return $data;
     }
 }
