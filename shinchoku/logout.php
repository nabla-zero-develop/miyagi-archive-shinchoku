<?php
session_start();

require_once("common_define.php");

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

session_destroy();

header("Location: " . $ROUTE_MAP["login"]);
