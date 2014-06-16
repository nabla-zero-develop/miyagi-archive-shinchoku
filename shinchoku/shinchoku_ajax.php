<?php
require_once("_config.php");

// TODO: Ajaxからの通信かどうか確認する

$categoryid = htmlspecialchars($_GET['categoryid']);
$shinchokuDate = htmlspecialchars($_GET['date']);

if ($categoryid == null || $shinchokuDate == null) {
    exit();
}

$dsn = "mysql:host=" . $db["host"] . ";charset=utf8";
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);

if ($categoryid == 1) {
    $holderTable = "miyagi_archive_ken.holder";
    $joinCondition = "a.holderid=b.id";
} elseif ($categoryid == 2) {
    $holderTable = "miyagi_archive_shichouson.holder";
    $joinCondition = "a.holderid=b.id";
} elseif ($categoryid == 3) {
    $holderTable = "miyagi_archive_shichouson.sikucyoson";
    $joinCondition = "TRUNCATE(a.holderid/10, 0)=b.code";
} else {
    exit();
}

$pdo = new PDO($dsn, $db["user"], $db["password"], $options);

$stmt = $pdo->prepare(
    "SELECT b.name, a.content_num, a.copyright_num, a.imageright_num, a.complete_num, a.complete_num / a.content_num AS complete_percent" .
    " FROM miyagi_archive_shinchoku.daily_shinchoku a JOIN " . $holderTable . " b ON " . $joinCondition .
    " WHERE a.categoryid=? AND DATE(a.shinchoku_date)=?"
);

$stmt->execute(array($categoryid, $shinchokuDate));

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-type: application/json; charset=utf-8");
echo preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {
        return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
    },
    json_encode($result)
    );
