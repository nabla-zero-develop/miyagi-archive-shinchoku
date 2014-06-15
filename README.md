進捗管理システム
========

使用ライブラリ
--------
shinchoku/css/jquery-ui-1.10.4.custom.min.css ... jQuery UI v1.10.4  
shinchoku/js/jquery-1.11.1.min.js ... jQuery v1.11.1  
shinchoku/js/jquery-ui-1.10.4.custom.min.js ... jQuery UI v1.10.4  
shinchoku/js/jquery.ui.datepicker-ja.min.js ... Struts2-jQuery(datepicker日本語化用)  

定期実行プログラム
--------
# 実行方法
`mysql -u <ユーザ名> -p<パスワード> < <sqlファイルが存在するディレクトリ>/shinchoku.sql`

# 定期実行設定
production_program/shinchoku.sqlを任意のディレクトリにコピーし、cronに上記実行方法を追記する
