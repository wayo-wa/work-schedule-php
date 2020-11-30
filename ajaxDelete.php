<?php
 require('function.php');
 //ログイン認証
 require('auth.php');

 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('AjaxDelete');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();

 //=======================================
 // Ajax処理
 //=======================================
 if(isset($_POST['stampingid']) && isset($_SESSION['user_id'])) {
     debug('AjaxでPOST送信されました。');
     
     $st_id = $_POST['stampingid'];
     $u_id = $_SESSION['user_id'];
     
     debug('st_idは:'.print_r($st_id, true));
     debug('u_idは:'.print_r($u_id, true));

     try {
         $dbh = dbConnect();
         $sql = 'SELECT * FROM stamping WHERE id = :st_id AND user_id = :u_id';
         $data = array(':st_id' => $st_id, ':u_id' => $u_id);
         $stmt = queryPost($dbh, $sql, $data);
         $resultCount = $stmt->rowCount();
         if(!empty($resultCount)) {
            $sql = 'DELETE FROM stamping WHERE id = :st_id AND user_id = :u_id';
            $data = array(':st_id' => $st_id, ':u_id' => $u_id);
            $stmt = queryPost($dbh, $sql, $data);
            $_SESSION['msg_success'] = SUC03;//削除しました
        }
    }catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
 }

 debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理終了');
?>
