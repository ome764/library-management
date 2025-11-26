<?php
// db.php - Database connection
header('Content-Type: text/html; charset=utf-8');

$host = '127.0.0.1';
$db   = 'library';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // If database doesn't exist, create it
    if ($e->getCode() == 1049) {
        try {
            $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
            $pdo->exec("CREATE DATABASE `$db`");
            $pdo->exec("USE `$db`");
            echo "تم إنشاء قاعدة البيانات بنجاح!";
        } catch (PDOException $e2) {
            die("فشل إنشاء قاعدة البيانات: " . $e2->getMessage());
        }
    } else {
        die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
    }
}
?>