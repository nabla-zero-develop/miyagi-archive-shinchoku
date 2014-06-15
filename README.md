進捗管理システム
========

使用ライブラリ
--------
shinchoku/css/jquery-ui ... jQuery UI v1.10.4  
shinchoku/css/tablesorter ... tablesorter v2.0.5  
shinchoku/js/jquery-1.11.1.min.js ... jQuery v1.11.1  
shinchoku/js/jquery-ui-1.10.4.custom.min.js ... jQuery UI v1.10.4  
shinchoku/js/jquery.ui.datepicker-ja.min.js ... Struts2-jQuery(datepicker日本語化用)  
shinchoku/js/jquery.tablesorter.min.js ... tablesorter v2.0.5  

定期実行プログラム
--------
### 実行方法
`mysql -u <ユーザ名> -p<パスワード> < <sqlファイルが存在するディレクトリ>/shinchoku.sql`

### 定期実行の設定方法
production_program/shinchoku.sqlを任意のディレクトリにコピーし、cronに上記実行方法を追記する

Web設定方法
--------
### 設定方法
1. shinchokuフォルダを公開場所にコピーする。
2. shinchokuフォルダの直下に`_config.php`という名前のファイルを作成する。
3. 新規作成した`_config.php`に下記の内容を記述する。環境に合わせて設定する。

<?php  
$db["host"] = "MySQLが動作しているサーバへのURL";  
$db["user"] = "ユーザ名";  
$db["password"] = "パスワード";  
