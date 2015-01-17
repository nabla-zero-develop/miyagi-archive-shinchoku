<?php
session_start();

if (!isset($_SESSION["USERNAME"])) {
    exit();
}

if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    exit();
}

require_once("_config.php");
require_once("common_define.php");

$categoryid = htmlspecialchars($_GET['categoryid']);
$shinchokuDate = htmlspecialchars($_GET['date']);

$username = $_SESSION["USERNAME"];
$usertype = $_SESSION["USERTYPE"];

if ($categoryid == null || $shinchokuDate == null || $username == null || $usertype == null) {
    exit();
}

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
    $joinUsersTable = "INNER JOIN miyagi_archive_ken.users c ON a.holderid=c.holderid";
    $onlyCurrentUser = "AND c.username=?";
    $additionalParameter = array($username);
} elseif ($usertype == USERTYPE_SHICHOUSON) {
    $joinUsersTable = "INNER JOIN miyagi_archive_shichouson.users c ON a.holderid=c.holderid";
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
    if ($categoryid == 1 || $categoryid == 2) {
        if ($categoryid == 1) {
            $holderTable = "miyagi_archive_ken.holder";
        } elseif ($categoryid == 2) {
            $holderTable = "miyagi_archive_shichouson.holder";
        }

        $sql = <<< SQL
SELECT
    b.name,
    a.content_num,
    a.copyright_num,
    a.imageright_num,
    a.complete_num,
    TRUNCATE(a.complete_num*100/a.content_num, 1) AS complete_percent
FROM miyagi_archive_shinchoku.daily_shinchoku a JOIN $holderTable b ON a.holderid=b.id $joinUsersTable
WHERE a.categoryid=? AND DATE(a.shinchoku_date)=? $onlyCurrentUser
SQL;

        $baseParameter = array($categoryid, $shinchokuDate);
    } elseif ($categoryid == 3) {
        $sql = <<< SQL
SELECT
    IFNULL(b.name, a.holderid) AS name,
    a.content_num,
    a.copyright_num,
    a.imageright_num,
    a.complete_num,
    TRUNCATE(a.complete_num*100/a.content_num, 1) AS complete_percent
FROM miyagi_archive_shinchoku.daily_shinchoku a LEFT JOIN miyagi_archive_shichouson.sikucyoson b ON a.holderid+4000=b.code $joinUsersTable
WHERE a.categoryid=? AND DATE(a.shinchoku_date)=? AND a.holderid<1000 $onlyCurrentUser
UNION ALL
SELECT
    IFNULL(CONCAT(b.department, b.section, b.corporate_body), a.holderid) AS name,
    a.content_num,
    a.copyright_num,
    a.imageright_num,
    a.complete_num,
    TRUNCATE(a.complete_num*100/a.content_num, 1) AS complete_percent
FROM miyagi_archive_shinchoku.daily_shinchoku a LEFT JOIN miyagi_archive_shinchoku.prefectural_department b ON a.holderid=b.code $joinUsersTable
WHERE a.categoryid=? AND DATE(a.shinchoku_date)=? AND a.holderid>=1000 $onlyCurrentUser
SQL;

        $baseParameter = array($categoryid, $shinchokuDate, $categoryid, $shinchokuDate);
    } else {
        exit();
    }
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
WHERE a.holderid>=100002 $onlyCurrentUser
GROUP BY a.holderid
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
GROUP BY a.holderid
SQL;
    } elseif ($categoryid == 3) {
        $sql = <<< SQL
SELECT
    IFNULL(b.name, a.municipality_id) AS name,
	COUNT(municipality_id) AS content_num,
	COUNT(IF(copyright!=0, 1, NULL)) AS copyright_num,
	COUNT(IF(imageright!=0, 1, NULL)) AS imageright_num,
	COUNT(IF(copyright!=0 AND imageright!=0, 1, NULL)) AS complete_num,
    TRUNCATE(COUNT(IF(copyright!=0 AND imageright!=0, 1, NULL)) * 100 / COUNT(municipality_id), 1) AS complete_percent
FROM miyagi_archive_shinchoku.digital_team_shinchoku a LEFT JOIN miyagi_archive_shichouson.sikucyoson b ON a.municipality_id+4000=b.code
WHERE a.municipality_id<1000
GROUP BY municipality_id
UNION ALL
SELECT
    IFNULL(CONCAT(b.department, b.section, b.corporate_body), a.municipality_id) AS name,
	COUNT(municipality_id) AS content_num,
	COUNT(IF(copyright!=0, 1, NULL)) AS copyright_num,
	COUNT(IF(imageright!=0, 1, NULL)) AS imageright_num,
	COUNT(IF(copyright!=0 AND imageright!=0, 1, NULL)) AS complete_num,
    TRUNCATE(COUNT(IF(copyright!=0 AND imageright!=0, 1, NULL)) * 100 / COUNT(municipality_id), 1) AS complete_percent
FROM miyagi_archive_shinchoku.digital_team_shinchoku a LEFT JOIN miyagi_archive_shinchoku.prefectural_department b ON a.municipality_id=b.code
WHERE a.municipality_id>=1000
GROUP BY municipality_id
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
