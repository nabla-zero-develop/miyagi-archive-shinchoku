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
shinchoku/js/jquery.parse.min.js ... Papa Parse v2.1.4  
shinchoku/js/js/jquery.fileupload.js ... jQuery File Upload v9.5.7  
shinchoku/js/js/jquery.fileupload-ui.js ... jQuery File Upload v9.5.7  
shinchoku/upload ... jQuery File Upload v9.5.7  

定期実行プログラム
--------
### 定期実行の設定方法
1. production_program/shinchoku.sqlを任意のディレクトリにコピー
2. crontabでエディタ起動  
`> crontab -e`
3. 下記を記述する（毎日2時に実行の場合）  
`0 2 * * * mysql -u <ユーザ名> -p<パスワード> < <sqlファイルが存在するディレクトリ>/shinchoku.sql`

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

制限事項
--------
・IE8ではCVSをアップロードするときに内容をプレビューすることはできません
