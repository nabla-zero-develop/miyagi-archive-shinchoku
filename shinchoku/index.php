<?php
session_start();

require_once("common_define.php");

if (!isset($_SESSION["USERNAME"])) {
    header("Location: " . $ROUTE_MAP["login"]);
    exit;
}

$isShowDepartment = $_SESSION["USERTYPE"] == USERTYPE_KEN || $_SESSION["USERTYPE"] == USERTYPE_SHINCHOKU;
$isShowMunicipalities = $_SESSION["USERTYPE"] == USERTYPE_SHICHOUSON || $_SESSION["USERTYPE"] == USERTYPE_SHINCHOKU;
$isShowDigitalTeam = $_SESSION["USERTYPE"] == USERTYPE_SHINCHOKU;
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
<div class="top-area">
    <div data-title>進捗管理システム</div>
    <div data-userinfo><?php echo $_SESSION["NICKNAME"] ?>でログイン中<br><a href="logout.php">ログアウト</a></div>
    <div data-clearfix>
</div>
<div class="tabs">
    <ul>
<?php
if ($isShowDepartment) {
?>
        <li><a href="#department">県各部局</a></li>
<?php
}

if ($isShowMunicipalities) {
?>
        <li><a href="#municipalities">市町村</a></li>
<?php
}

if ($isShowDigitalTeam) {
?>
        <li><a href="#digital_team">デジタル化</a></li>
<?php
}
?>
    </ul>
<?php
if ($isShowDepartment) {
?>
    <div id="department">
        <div class="datepicker"></div>
        <div class="shinchoku-area">
            <table class="tablesorter">
                <thead><tr><th>県部局名</th><th>受入件数</th><th>権利処理済件数</th><th>肖像権処理件数</th><th>公開準備完了件数</th><th>公開準備完了割合</th></tr></thead>
                <tbody></tbody>
            </table>
            <div>日付を選択してください</div>
            <img src="images/gif-load.gif" class="gone">
        </div>
    </div>
<?php
}

if ($isShowMunicipalities) {
?>
    <div id="municipalities">
        <div class="datepicker"></div>
        <div class="shinchoku-area">
            <table class="tablesorter">
                <thead><tr><th>市町村名</th><th>受入件数</th><th>権利処理済件数</th><th>肖像権処理件数</th><th>公開準備完了件数</th><th>公開準備完了割合</th></tr></thead>
                <tbody></tbody>
            </table>
            <div>日付を選択してください</div>
            <img src="images/gif-load.gif" class="gone">
        </div>
    </div>
<?php
}

if ($isShowDigitalTeam) {
?>
    <div id="digital_team">
        <span class="cross-browser-button fileinput-button">
            <span>CSVファイル選択</span>
            <input type="file" id="csv_select">
        </span>
        <div id="csv_dialog">
            <div id="csv_preview_message"></div>
            <table id="csv_preview"></table>
        </div>
        <div id="uploading"><div><img src="images/gif-load.gif"></div><div>アップロード中</div></div>
        <div id="upload_result"></div>
        <div class="datepicker"></div>
        <div class="shinchoku-area">
            <table class="tablesorter">
                <thead><tr><th>県各部局・市町村名</th><th>受入件数</th><th>権利処理済件数</th><th>肖像権処理件数</th><th>公開準備完了件数</th><th>公開準備完了割合</th></tr></thead>
                <tbody></tbody>
            </table>
            <div>日付を選択してください</div>
            <img src="images/gif-load.gif" class="gone">
        </div>
    </div>
<?php
}
?>
</div>
</body>
</html>
