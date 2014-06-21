<?php
session_start();

require_once("common_define.php");

if (isset($_SESSION["USERNAME"])) {
    header("Location: ". $ROUTE_MAP["index"]);
    exit;
}

$errorMessage = "";

if (isset($_POST["login"])) {
    require("_config.php");

    try {
        $dsn = "mysql:host=" . $db["host"] . ";charset=utf8";
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );

        $pdo = new PDO($dsn, $db["user"], $db["password"], $options);

        // TODO: Should add miyagi_archive_shinchoku
        $sql = <<< SQL
SELECT nickname, {$cst(USERTYPE_KEN)} AS usertype FROM miyagi_archive_ken.users WHERE username=? AND password=?
UNION ALL
SELECT nickname, {$cst(USERTYPE_SHICHOUSON)} AS usertype FROM miyagi_archive_shichouson.users WHERE username=? AND password=?
UNION ALL
SELECT nickname, {$cst(USERTYPE_SHINCHOKU)} AS usertype FROM miyagi_archive_shinchoku.users WHERE username=? AND password=?;
SQL;

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array($_POST["username"], $_POST["password"], $_POST["username"], $_POST["password"], $_POST["username"], $_POST["password"]));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            session_regenerate_id(true);
            $_SESSION["USERNAME"] = $_POST["username"];
            $_SESSION["NICKNAME"] = $result["nickname"];
            $_SESSION["USERTYPE"] = $result["usertype"];
            header("Location: ". $ROUTE_MAP["index"]);
            exit;
        } else {
            $errorMessage = "ユーザ名あるいはパスワードに誤りがあります。";
        }
    } catch (Exception $e) {
        $errorMessage = "システムエラーのため、ログインに失敗しました: " . $e->getMessage();
    }

    session_destroy();
}

$viewUserId = htmlspecialchars($_POST["username"], ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="css/login.css" />
<script>
// IE8ではautofocus属性が使用できないため、JavaScriptでフォーカスをあてる
window.onload = function () {
    document.getElementById("username").focus();
}
</script>
<meta charset="UTF-8">
<title>進捗管理システムログイン</title>
</head>
<body>
<div data-title>進捗管理システム</div>
<form action="<?php print($_SERVER['PHP_SELF']) ?>" method="POST">
    <fieldset>
        <legend>ログインフォーム</legend>
        <div><?php echo $errorMessage ?></div>
        <label for="username">ユーザ名</label><input type="text" id="username" name="username" value="<?php echo $viewUserId ?>"><br>
        <label for="password">パスワード</label><input type="password" id="password" name="password"><br>
        <input type="submit" id="login" name="login" value="ログイン">
    </fieldset>
</form>
</body>
</html>
