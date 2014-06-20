<?php
session_start();

if (isset($_SESSION["USERID"])) {
    header("Location: index.html");
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

        $sql = <<< SQL
SELECT username, nickname, 1 AS categoryid FROM miyagi_archive_ken.users WHERE username=? AND password=?
UNION ALL
SELECT username, nickname, 2 AS categoryid FROM miyagi_archive_shichouson.users WHERE username=? AND password=?;
SQL;

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array($_POST["userid"], $_POST["password"], $_POST["userid"], $_POST["password"]));

        $result = $stmt->fetch(PDO::FETCH_NUM);

        if ($result) {
            // TODO: under construction
            header("Location: index.php");
            exit;
        } else {
            $errorMessage = "ユーザ名あるいはパスワードに誤りがあります。";
        }
    } catch (Exception $e) {
        $errorMessage = "システムエラーのため、ログインに失敗しました: " . $e->getMessage();
    }
}

$viewUserId = htmlspecialchars($_POST["userid"], ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>進捗管理システムログイン</title>
</head>
<body>
<h1>進捗管理システム</h1>
<form action="<?php print($_SERVER['PHP_SELF']) ?>" method="POST">
    <fieldset>
        <legend>ログインフォーム</legend>
        <div><?php echo $errorMessage ?></div>
        <label for="userid">ユーザ名</label><input type="text" id="userid" name="userid" value="<?php echo $viewUserId ?>"><br>
        <label for="password">パスワード</label><input type="password" id="password" name="password"><br>
        <input type="submit" id="login" name="login" value="ログイン">
    </fieldset>
</form>
</body>
</html>
