<?php
session_start();

if (!isset($_SESSION["USERNAME"])) {
    exit();
}

if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    exit();
}

require_once("_config.php");

$categoryid = htmlspecialchars($_GET['categoryid']);
$shinchokuDate = htmlspecialchars($_GET['date']);

$username = $_SESSION["USERNAME"];
$usertype = $_SESSION["USERTYPE"];

if ($categoryid == null || $shinchokuDate == null || $username == null || $usertype == null) {
    exit();
}

define("USERTYPE_KEN", 1);
define("USERTYPE_SHICHOUSON", 2);
define("USERTYPE_SHINCHOKU", 3);

if ($usertype != USERTYPE_SHINCHOKU && $usertype != $categoryid) {
    exit();
}

// NOTE: 12時を過ぎてから進捗を集計するため、そのままの日付で検索すると
// 1日前の進捗状況を表示することになる。
// 1日加算することで意図通りの表示ができる
$shinchokuDate = date('Y-m-d', strtotime($shinchokuDate . " +1 day"));

$dsn = "mysql:host=" . $db["host"] . ";charset=utf8";
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);

$dt = new DateTime("+1 day");
$today = $dt->format('Y-m-d');

$pdo = new PDO($dsn, $db["user"], $db["password"], $options);


if ($usertype == USERTYPE_KEN) {
    $joinUsersTable = "INNER JOIN miyagi_archive_ken.users c ON c.holderid=a.holderid";
    $onlyCurrentUser = "AND c.username=?";
    $additionalParameter = array($username);
} elseif ($usertype == USERTYPE_SHICHOUSON) {
    $joinUsersTable = "INNER JOIN miyagi_archive_shichouson.users c ON c.holderid=a.holderid";
    $onlyCurrentUser = "AND c.username=?";
    $additionalParameter = array($username);
} elseif ($usertype == USERTYPE_SHINCHOKU) {
    $joinUsersTable = "";
    $onlyCurrentUser = "";
    $additionalParameter = array();
} else {
    exit();
}

if (strtotime($shinchokuDate) != strtotime($today)) {
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

    $sql = <<< SQL
SELECT b.name, a.content_num, a.copyright_num, a.imageright_num, a.complete_num, TRUNCATE(a.complete_num*100/a.content_num, 1) AS complete_percent
FROM miyagi_archive_shinchoku.daily_shinchoku a JOIN $holderTable b ON $joinCondition $joinUsersTable
WHERE a.categoryid=? AND DATE(a.shinchoku_date)=? $onlyCurrentUser
SQL;

    $baseParameter = array($categoryid, $shinchokuDate);
} else {
    if ($categoryid == 1) {
        $sql = <<< SQL
SELECT
	b.name,
	COUNT(a.holderid) AS content_num,
	COUNT(IF(md_copyright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS copyright_num,
	COUNT(IF(md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS imageright_num,
	COUNT(IF(md_copyright!=0 AND md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS complete_num,
    TRUNCATE(COUNT(IF(md_copyright!=0 AND md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) * 100 / COUNT(a.holderid), 1) AS complete_percent
FROM miyagi_archive_ken.content a JOIN miyagi_archive_ken.holder b ON a.holderid=b.id $joinUsersTable
WHERE a.holderid>=121000 $onlyCurrentUser
GROUP BY a.holderid;
SQL;
    } elseif ($categoryid == 2) {
        $sql = <<< SQL
SELECT
	b.name,
	COUNT(a.holderid) AS content_num,
	COUNT(IF(md_copyright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS copyright_num,
	COUNT(IF(md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS imageright_num,
	COUNT(IF(md_copyright!=0 AND md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) AS complete_num,
    TRUNCATE(COUNT(IF(md_copyright!=0 AND md_imageright!=0 AND kiso_editflag=0 AND kihon_editflag=0, 1, NULL)) * 100 / COUNT(a.holderid), 1) AS complete_percent
FROM miyagi_archive_shichouson.content a JOIN miyagi_archive_shichouson.holder b ON a.holderid=b.id $joinUsersTable
WHERE a.holderid<990 $onlyCurrentUser
GROUP BY a.holderid;
SQL;
    } elseif ($categoryid == 3) {
        $sql = <<< SQL
SELECT
	b.name,
	COUNT(municipality_id) AS content_num,
	COUNT(IF(copyright!=0, 1, NULL)) AS copyright_num,
	COUNT(IF(imageright!=0, 1, NULL)) AS imageright_num,
	COUNT(IF(copyright!=0 AND imageright!=0, 1, NULL)) AS complete_num,
    TRUNCATE(COUNT(IF(copyright!=0 AND imageright!=0, 1, NULL)) * 100 / COUNT(municipality_id), 1) AS complete_percent
FROM miyagi_archive_shinchoku.digital_team_shinchoku a JOIN miyagi_archive_shichouson.sikucyoson b ON TRUNCATE(municipality_id/10, 0)=b.code
GROUP BY municipality_id;
SQL;
    } else {
        exit();
    }

    $baseParameter = array();
}

$stmt = $pdo->prepare($sql);
$stmt->execute(array_merge($baseParameter, $additionalParameter));
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-type: application/json; charset=utf-8");
echo preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {
        return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
    },
    json_encode($result)
    );
