<?php

// /**
//  * Автор: Максим Сарамудов
//  * 
//  * Дата реализации: 02.11.2022 13:20
//  * 
//  * Подключение к базе данных
// */

$driver = 'mysql';
$host = "localhost";
$port = 3306;
$database = 'test-assigment';
$username = 'root';
$password = 'root';
$charset = 'utf8';

$dsn = "{$driver}:host={$host};dbname={$database};charset={$charset};port={$port}";

try {
  $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
  die("Не удалось подключиться к базе данных: {$e->getMessage()}");
}

?>