<?php
require('function.php');

 debug('[[[[[[[[[[[[[[[[[[[[');
 debug('ログアウト');
 debug('[[[[[[[[[[[[[[[[[[[[');
 debugLogStart();

 debug('ログアウトします');
 session_destroy();
 header("Location:index.php");
 exit();