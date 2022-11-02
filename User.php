<?php

/**
 * Автор: Максим Сарамудов
 * 
 * Дата реализации: 02.11.2022 13:20
 * 
 * Дата изменения: 03.11.2022 00:38
 * 
 * Утилита для работы с базой данных
 */

declare(strict_types=1);
require_once "./connection.php";

/**
 * Класс User
 * Класс предназначен для работы с базой данных. Класс осуществляет сохранение, удаление, форматирование пользователя, преобразование пола из двоичной системы в строку и преобразование даты рождения в количество полных лет.
 */
class User 
{
  protected int $id;
  protected string $name;
  protected string $surname;
  protected DateTime $dateOfBirth;
  protected int $gender;
  protected string $cityBirth;

  /**
   * Конструктор класса User.
   * Конструктор предназначен для инициализации полей класса. 
   * Если переданный id существует в базе данных, то поля заполняются полученными данными из базы данных, иначе валидируются переданные параметры и в случае успеха поля заполняются этими данными.
   * 
   * @param int $id
   * @param string $name
   * @param string $surname
   * @param DateTime $dateOfBirth
   * @param int $gender
   * @param string $cityBirth
   * @return void
   */
  function __construct(int $id, string $name = '', string $surname = '',
    DateTime $dateOfBirth = null, int $gender = 0, string $cityBirth = ''
  ) {
    $regexNumbers = "/[0-9]+/u";

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

      echo 'Пользователь уже есть в базе данных';
      return;
    }

    if (($gender > 1) 
        || ($gender < 0)
    ) {
      die('Ошибка пол должен быть 0 или 1');
    }

    if (empty(trim($name))) {
      die('Поле имя не может быть пустым');
    }
    
    if (preg_match($regexNumbers, $name)) {
      die('Поле имя может содержать только буквы');
    }

    if (empty(trim($surname))) {
      die('Поле фамилия не может быть пустым');
    }

    if (preg_match($regexNumbers, $surname)) {
      die('Поле фамилия может содержать только буквы');
    }

    if (empty(trim($cityBirth))) {
      die('Поле город рождения не может быть пустым');
    }

    $this->id = $id;
    $this->name = $name;
    $this->surname = $surname;
    $this->dateOfBirth = $dateOfBirth ? new DateTime('now') : $dateOfBirth;
    $this->cityBirth = $cityBirth;
    $this->gender = $gender;
    $this->save();
  }

  /**
   * Сохранение пользователя в базу данных.
   * 
   * @return void
   */
  public function save(): void
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

      echo "Успешное сохранение пользователя";
    } catch (PDOException $e) {
      die("Ошибка сохранения пользователя: {$e->getMessage()}");
    }
  }

  /**
   * Удаление пользователя из базы данных по полю id.
   * 
   * @return void
   */
  public function delete(): void
  {
    try {
      $query = $GLOBALS['pdo']->prepare("DELETE FROM `users` WHERE `id` = :id");
      $query->execute(array(
        'id' => $this->id,
      ));

      echo "Успешное удаление пользователя с id = {$this->id}";
    } catch (PDOException $e) {
      die("Ошибка удаления пользователя: {$e->getMessage()}");
    }
  }

  /**
   * Форматирование пользователя с преобразованием возраста и даты рождения.
   * 
   * @return stdClass
   */
  public function formatUser(): stdClass
  {
    $years = self::convertDateOfBirthInYears($this->dateOfBirth);
    $gender = self::convertGenderInString($this->gender);

    $object = new stdClass();
    $object->id = $this->id;
    $object->name = $this->name;
    $object->surname = $this->surname;
    $object->dateOfBirth = $years;
    $object->cityBirth = $this->cityBirth;
    $object->gender = $gender;

    return $object;
  }

  /**
   * Преобразование пола из двоичной системы в строку.
   * 
   * @param int $gender
   * @return string
   */
  static public function convertGenderInString(int $gender): string
  {
    if ($gender === 0) {
      return 'Муж';
    } else if ($gender === 1) {
      return 'Жен';
    }
  }

  /**
   * Преобразование даты рождения в количество полных лет.
   * 
   * @param DateTime $dateOfBirth
   * @return int
   */
  static public function convertDateOfBirthInYears(DateTime $dateOfBirth): int
  {
    $years = (int)floor((time() - $dateOfBirth->getTimestamp()) / (60 * 60 * 24 * 365));
    return $years;
  }
}

?>