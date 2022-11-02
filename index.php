<?php

declare(strict_types=1);
require_once './User.php';
require_once './Users.php';

// Тестирование класса User
$alex = new User(10, 'Alex', 'Burakov', new DateTime('2002-05-02'), 0, 'Минск');
debugPrint($alex);
debugPrint($alex->formatUser());
debugPrint(User::convertGenderInString(2));

// Тестирование класса Users
$users = new Users([1, '32', 'gdfgdsg', 10, 4, 5]);
debugPrint($users->getListUsers());
$users->deleteListUsers();

/**
 * Функция вывода информации для тестирования
 * 
 * @param midex $arg
 * @return void
 */
function debugPrint(mixed $arg): void
{
  echo '<pre>';
  var_dump($arg);
  echo '</pre>';
}

?>