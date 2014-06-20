<?php
session_start();

if (!isset($_SESSION["USERID"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="css/jquery-ui/jquery-ui-1.10.4.custom.min.css" />
<link rel="stylesheet" href="css/tablesorter/style.css" />
<link rel="stylesheet" href="css/jquery.fileupload.css" />
<link rel="stylesheet" href="css/cross-browser-css-gradient-buttons.css" />
<link rel="stylesheet" href="css/shinchoku.css" />
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="js/jquery.ui.datepicker-ja.min.js"></script>
<script src="js/jquery.tablesorter.min.js"></script>
<script src="js/jquery.parse.min.js"></script>
<script src="js/jquery.fileupload.js"></script>
<script src="js/jquery.iframe-transport.js"></script>
<script src="js/shinchoku.js"></script>
<meta charset="UTF-8">
<title>進捗管理システム</title>
</head>
<body>
<h1>進捗管理システム</h1>
<div class="tabs">
    <ul>
        <li><a href="#department">県各部局</a></li>
        <li><a href="#municipalities">市町村</a></li>
        <li><a href="#digital_team">デジタル化</a></li>
    </ul>
    <div id="department">
        <div class="datepicker"></div>
        <div class="shinchoku-area">
            <table class="tablesorter">
                <thead><tr><th>県部局名</th><th>受入総数</th><th>権利処理済件数</th><th>肖像権処理件数</th><th>公開準備完了件数</th><th>公開準備完了割合</th></tr></thead>
                <tbody></tbody>
            </table>
            <div>日付を選択してください</div>
            <img src="images/gif-load.gif" class="gone">
        </div>
    </div>
    <div id="municipalities">
        <div class="datepicker"></div>
        <div class="shinchoku-area">
            <table class="tablesorter">
                <thead><tr><th>市町村名</th><th>受入総数</th><th>権利処理済件数</th><th>肖像権処理件数</th><th>公開準備完了件数</th><th>公開準備完了割合</th></tr></thead>
                <tbody></tbody>
            </table>
            <div>日付を選択してください</div>
            <img src="images/gif-load.gif" class="gone">
        </div>
    </div>
    <div id="digital_team">
        <span class="cross-browser-button fileinput-button">
            <span>CSVファイル選択</span>
            <input type="file" id="csv_select">
        </span>
        <div id="csv_dialog">
            <table id="csv_preview"></table>
            <div id="csv_preview_not_support" class="gone">ファイルをアップロードしますか？</div>
        </div>
        <div id="uploading"><div><img src="images/gif-load.gif"></div><div>アップロード中</div></div>
        <div id="upload_result"></div>
        <div class="datepicker"></div>
        <div class="shinchoku-area">
            <table class="tablesorter">
                <thead><tr><th>市町村名</th><th>受入総数</th><th>権利処理済件数</th><th>肖像権処理件数</th><th>公開準備完了件数</th><th>公開準備完了割合</th></tr></thead>
                <tbody></tbody>
            </table>
            <div>日付を選択してください</div>
            <img src="images/gif-load.gif" class="gone">
        </div>
    </div>
</div>
</body>
</html>
