<?php
$sqliteFile = __DIR__ . '/../database/database.sqlite';
$mysqlHost = '127.0.0.1';
$mysqlPort = 3306;
$mysqlDb = 'laravel';
$mysqlUser = 'root';
$mysqlPass = '';

if (!file_exists($sqliteFile)) {
    echo "SQLite file not found: $sqliteFile\n";
    exit(1);
}

try {
    $sqlite = new PDO('sqlite:' . $sqliteFile, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $mysql = new PDO("mysql:host=$mysqlHost;port=$mysqlPort;dbname=$mysqlDb;charset=utf8mb4", $mysqlUser, $mysqlPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $tablesStmt = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';");
    $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
    if (!$tables) {
        echo "No tables found in SQLite DB.\n";
        exit(0);
    }

    $mysql->exec('SET FOREIGN_KEY_CHECKS=0');

    foreach ($tables as $table) {
        echo "Importing table: $table\n";
        $rows = $sqlite->query("SELECT * FROM \"$table\"")->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            echo "  no rows\n";
            continue;
        }
        $columns = array_keys($rows[0]);
        $colList = implode(', ', array_map(function($c){return "`$c`";}, $columns));
        $placeholders = implode(', ', array_map(function($c){return ':' . $c;}, $columns));
        $insertSql = "INSERT INTO `$table` ($colList) VALUES ($placeholders)";
        $insert = $mysql->prepare($insertSql);
        $count = 0;
        foreach ($rows as $r) {
            foreach ($r as $k => $v) {
                $insert->bindValue(':' . $k, $v);
            }
            try {
                $insert->execute();
                $count++;
            } catch (Exception $e) {
                // ignore duplicates or schema mismatches
            }
        }
        echo "  imported $count rows\n";
    }

    $mysql->exec('SET FOREIGN_KEY_CHECKS=1');
    echo "Import complete.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
