<?php

/**
 * Автор: Максим Сарамудов
 * 
 * Дата реализации: 02.11.2022 13:20
 * 
 * Утилита для работы с базой данных
 */

declare(strict_types=1);
require_once "./connection.php";

/**
 * TODO:
 * [+] Создать функцию форматирование человека
 * [*] Исправить конструктор.
 */
class User 
{
  public int $id;
  public string $name;
  public string $surname;
  public DateTime $dateOfBirth;
  public int $gender;
  public string $cityBirth; 

  function __construct(int $id, string $name = '', string $surname = '',
    DateTime $dateOfBirth = new DateTime('now'), int $gender = 0, string $cityBirth = ''
  ) {
    $query = $GLOBALS['pdo']->prepare("SELECT * FROM `users` WHERE `id` = :id");
    $query->execute(array(
      'id' => $id,
    ));
    $result = $query->fetch();

    if ($result) {
      $this->id = (int)$result['id'];
      $this->name = $result['name'];
      $this->surname = $result['surname'];
      $this->dateOfBirth = new DateTime($result['date_of_birth']);
      $this->cityBirth = $result['city_birth'];
      $this->gender = (int)$result['gender'];
      echo '<pre>';
      var_dump($this);
      echo '</pre>';
      return;
    }

    if (($gender > 1) 
        || ($gender < 0)
    ) {
      die('Ошибка пол должен быть 0 или 1');
    }

    if (empty($name)) {
      die('Поле Имя не может быть пустым');
    }

    if (empty($surname)) {
      die('Поле фамилия не может быть пустым');
    }

    if (empty($cityBirth)) {
      die('Поле город рождения не может быть пустым');
    }

    $this->id = $id;
    $this->name = $name;
    $this->surname = $surname;
    $this->dateOfBirth = $dateOfBirth;
    $this->cityBirth = $cityBirth;
    $this->gender = $gender;
  }

  public function save()
  {
    try {
      $query = $GLOBALS['pdo']->prepare("INSERT INTO `users` (`id`, `name`, `surname`, `date_of_birth`, `gender`, `city_birth`) VALUES (:id, :name, :surname, :dateOfBirth, :gender, :cityBirth)");
      $query->execute(array(
        'id' => $this->id,
        'name' => $this->name,
        'surname' => $this->surname,
        'dateOfBirth' => date('Y-m-d', $this->dateOfBirth->getTimestamp()),
        'gender' => $this->gender,
        'cityBirth' => $this->cityBirth,
      ));

      echo "Успешное сохранения пользователя";
    } catch (PDOException $e) {
      die("Ошибка сохранения пользователя: {$e->getMessage()}");
    }
  }

  public function delete()
  {
    try {
      $query = $GLOBALS['pdo']->prepare("DELETE FROM `users` WHERE `id` = :id");
      $query->execute(array(
        'id' => $this->id,
      ));

      echo "Успешное удаления пользователя с id = {$this->id}";
    } catch (PDOException $e) {
      die("Ошибка удаления пользователя: {$e->getMessage()}");
    }
  }

  static public function formatDateOfBirthInYears(DateTime $dateOfBirth)
  {
    $years = floor((time() - $dateOfBirth->getTimestamp()) / (60 * 60 * 24 * 365));
    return $years;
  }


}

// var_dump(new DateTime('now', new DateTimeZone('Europe/Minsk')));
// $user = new User(2, 'ggdg', 'gdfgdg', new DateTime('2013-11-02'), 0, 'fgdsgsf');
$user1 = new User(3);
// $user->delete()
// var_dump(User::formatDateOfBirthInYears(new DateTime($user->dateOfBirth)));


?>