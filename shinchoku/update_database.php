<?php
define("COLUMN_COUNT", "5");

function updateDatabase($file_path, $uncheck_code) {
    require("_config.php");

    try {
        $dsn = "mysql:dbname=miyagi_archive_shinchoku;host=" . $db["host"] . ";charset=utf8";
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );

        $pdo = new PDO($dsn, $db["user"], $db["password"], $options);

        $stmt_select = $pdo->prepare('SELECT * FROM digital_team_shinchoku WHERE municipality_id=? AND barcode_id=?');
        $stmt_insert = $pdo->prepare('INSERT INTO digital_team_shinchoku (id, municipality_id, barcode_id, copyright, imageright, registration_date) VALUES (?, ?, ?, ?, ?, now())');
        $stmt_update = $pdo->prepare('UPDATE digital_team_shinchoku SET id=?, copyright=?, imageright=? WHERE municipality_id=? AND barcode_id=?');

        $stmtCheckShikucyoson = $pdo->prepare('SELECT code FROM miyagi_archive_shichouson.sikucyoson WHERE code=?+4000');
        $stmtCheckPrefecturalDepartment = $pdo->prepare('SELECT code FROM miyagi_archive_shinchoku.prefectural_department WHERE code=?');

        $pdo->beginTransaction();
        try {
            $fp = fopen($file_path, 'rb');

            // skip header
            fgetcsv($fp);

            while ($row = fgetcsv($fp)) {
                if ($row === array(null)) {
                    continue;
                }

                // {0: id, 1: municipality_id, 2: barcode_id, 3: copyright, 4: imageright}
                if (count($row) != COLUMN_COUNT) {
                    throw new RuntimeException('Invalid column detected');
                }

                if (!$uncheck_code) {
                    $municipalityId = $row[1];
                    if ($municipalityId < 1000) {
                        $stmtCheckShikucyoson->execute(array($municipalityId));
                        if ($stmtCheckShikucyoson->rowCount() == 0) {
                            // エラーメッセージの先頭が '!need_confirm'の場合、
                            // UploadHandler.php でユーザ確認が必要なものとして処理する
                            throw new RuntimeException('!need_confirm' . $municipalityId);
                        }
                        $stmtCheckShikucyoson->closeCursor();
                    } else {
                        $stmtCheckPrefecturalDepartment->execute(array($municipalityId));
                        if ($stmtCheckPrefecturalDepartment->rowCount() == 0) {
                            // エラーメッセージの先頭が '!need_confirm'の場合、
                            // UploadHandler.php でユーザ確認が必要なものとして処理する
                            throw new RuntimeException('!need_confirm' . $municipalityId);
                        }
                        $stmtCheckPrefecturalDepartment->closeCursor();
                    }
                }

                $stmt_select->execute(array($row[1], $row[2]));
                $notExist = ($stmt_select->rowCount() == 0);
                $stmt_select->closeCursor();

                if ($notExist) {
                    $stmt_insert->execute($row);
                } else {
                    $stmt_update->execute(array($row[0], $row[3], $row[4], $row[1], $row[2]));
                }
            }

            if (!feof($fp)) {
                throw new RuntimeException('CSV parsing error');
            }

            fclose($fp);
            $pdo->commit();
        } catch (Exception $e) {
            fclose($fp);
            $pdo->rollBack();
            throw $e;
        }
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
