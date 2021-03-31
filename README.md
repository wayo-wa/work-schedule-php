## WEBで勤怠管理。  
  
●使用ＯＳ：windows10  
●データベース：ClearDB Mysql  
●作成目的：PHP言語学習の為。  
●使用言語：HTML/SCSS/PHP/JS(jQuery,Ajax)  
●制作日数：約20日間

#### 【アプリケーション概要】　
&emsp;&emsp;勤務時間の登録・更新・削除、登録した過去の勤怠履歴の検索が行えるアプリケーションです。 
#### 【URL】&emsp;https://wk-sch2021.herokuapp.com/<br> 
#### 【テスト用アカウント】<br>
&emsp;&emsp;&emsp;・ メールアドレス&emsp;:&emsp;abcd123@com<br>
&emsp;&emsp;&emsp;・ パスワード&emsp;:&emsp;111111<br>
#### 【利用方法】<br>
&emsp;&emsp;1)&emsp;初回は「新規登録」よりユーザー登録（氏名、メールアドレス、パスワード）を行います。<br> 
&emsp;&emsp;2)&emsp;登録済みの場合は「ログイン」よりメールアドレス、パスワード入力でマイページに遷移します。<br> 
&emsp;&emsp;3)&emsp;「マイページ」は勤怠情報が表示されます。「年月検索」より過去の勤怠情報の検索も行えます。<br> 
&emsp;&emsp;4)&emsp;「本日打刻」より勤怠情報の登録が行えます。<br> 
&emsp;&emsp;5)&emsp;勤怠情報の修正・削除を行う場合は、「マイページ」の修正・削除ボタンより行います。<br> 
&emsp;&emsp;6)&emsp;「修正」ボタン押下後、本日打刻ページに遷移します。内容を修正して「更新する」を押下して下さい。<br>
&emsp;&emsp;7)&emsp;「削除」ボタン押下すると、選択された勤怠情報が削除されます。<br> 
#### 【余談】<br>
&emsp;&emsp;「マイページ」の「削除」処理について。画面遷移せず、削除処理だけを行いたいので、data属性に変数$val['id']<br> 
&emsp;&emsp;を付与し、ajaxDelete.phpへ値を渡し非同期通信(ajax)にて行いました。<br> 
```
<input class="p-btn c-btn c-btn--modify js-button-click" **data-stampingid="<?php echo $val['id']; ?>"** type="submit" value="削除">
```
#### トップ画面 

<img src="https://user-images.githubusercontent.com/73923419/104114721-f5b69100-534a-11eb-9a40-6933f1aea9ad.png" width="650px">
  
#### マイページ画面
（登録・修正・削除時にメッセージが出ます。）  
  
<img src="https://user-images.githubusercontent.com/73923419/104116314-d1fb4700-535a-11eb-8193-a52d5447878d.png" width="650px">


