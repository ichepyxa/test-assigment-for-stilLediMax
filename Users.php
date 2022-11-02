<?php

/**
 * Автор: Максим Сарамудов
 * 
 * Дата реализации: 03.11.2022 02:22
 * 
 * Утилита для работы с базой данных
 */

declare(strict_types=1);
require_once './index.php';

if (!class_exists('User')) {
  die('Класс User не обьявлен');
}

/**
 * Класс Users.
 * Класс предназначен для работы списка пользователей с базой данных. Класс осуществляет получения списка, удаление списка, проверку существующих пользователей.
 */
class Users
{
  protected array $identifiers;

  /**
   * Конструктор класса Users.
   * Конструктор предназначен для инициализации полей класса. 
   * Переданный массив с идентификаторами пользователей валидируется, также проверяется существуют ли записи в базе данных с такими идентификаторами, после чего валидный массив записывается в поле identifiers.
   * 
   * @param array $identifiers
   * @return void
   */
  function __construct(array $identifiers)
  {
    foreach ($identifiers as $key => $id) {
      if (is_numeric($id)) {
        $query = $GLOBALS['pdo']->prepare("SELECT * FROM `users` WHERE `id` > :idLess AND `id` < :idMore");
        $query->execute(array(
          'idMore' => (int)$id + 1,
          'idLess' => (int)$id - 1,
        ));
        $result = $query->fetch();

        if ($result) {
          echo "Пользователь с идентификатором: {$id} - существует в базе данных<br>";
          continue;
        }

        echo "Пользователь с идентификатором: {$id} - не существует в базе данных<br>";
        unset($identifiers[$key]);
      } else {
        echo "Данный элемент: {$id} - не является идентификатором<br>";
        unset($identifiers[$key]);
      }
    }

    $this->identifiers = $identifiers;
  }

  /**
   * Функция получения списка пользователей из базы данных по массиву идентификаторов.
   * 
   * @return array
   */
  public function getListUsers(): array
  {
    $list = array();

    foreach ($this->identifiers as $id) {
      array_push($list, new User((int)$id));
    }

    return $list;
  }

  /**
   * Функция удаления пользователей из базы данных по массиву идентификаторов.
   * 
   * @return void
   */
  public function deleteListUsers(): void
  {
    foreach ($this->identifiers as $id) {
      (new User((int)$id))->delete();
    }
  }
}

?>