<?php
// ルーティング
function createRouteMap() {
    $scheme = $_SERVER['HTTPS'] ? "https://" : "http://";
    $host = $_SERVER["HTTP_HOST"];
    $uri = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");

    return array(
        "index" => "$scheme$host$uri/index.php",
        "login" => "$scheme$host$uri/login.php"
    );
}

$ROUTE_MAP = createRouteMap();

// ユーザタイプ
define("USERTYPE_KEN", 1);
define("USERTYPE_SHICHOUSON", 2);
define("USERTYPE_SHINCHOKU", 3);

// ヒアドキュメントで使用するための関数
$cst = "cst";
function cst($constant) {
    return $constant;
}
