<?php

namespace php;

session_start();

require_once 'service/db.php';

use service\Db;
use PDO;

class Registration
{

  public static function dbconn()
  {
    $dbh = Db::get();
    return $dbh;
  }

  public function get_data()
  {
  }
  public static function createTableUsers()
  {
    $dbh = self::dbconn();
    if ($dbh) {
      $sql = 'CREATE TABLE IF NOT EXISTS users(
        id serial PRIMARY KEY,
        login varchar(255) NOT NULL,
        email varchar(100) NOT NULL,
        passward varchar(100) NOT NULL
  )';

      return $dbh->exec($sql);
    } else {
      return 'Having connection problems, contact the administrator';
    }
  }

  public static function create_data()
  {
    $dbh = self::dbconn();
    $LOGIN = $_POST['login'];
    $EMAIL = $_POST['email'];
    //var_dump($_POST);
    $PASSWARD = $_POST['password'];
    $CONFIRMPASS  = $_POST['password_confirm'];
    $hash = password_hash($PASSWARD, PASSWORD_DEFAULT);
    $sql = 'INSERT INTO users(login, email, passward) VALUES(:login, :email, :password)';
    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':login',  $LOGIN);
    $stmt->bindValue(':email', $EMAIL);
    $stmt->bindValue(':password', $hash);

    $res = $dbh->query("SELECT login FROM users WHERE login = '$LOGIN'");

    $result = $res->Fetch(PDO::FETCH_ASSOC);
    if ((isset($_POST["login"])) && (isset($_POST["password"]))) {
      if (empty($result && $CONFIRMPASS == $PASSWARD)) {
        $stmt->execute();
        header("Location:/registration");
        $LOGIN = NULL;
      }
      return 'Такой логин уже существует';
    }
  }
}

$FormReg = new Registration();
echo $FormReg->create_data();
?>


<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- <link rel="stylesheet" href="/assets/css/reset.css">
  <link rel="stylesheet" href="/assets/css/main.css"> -->
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

  <!-- <title>Галерея изображений | Список файлов</title> -->
</head>

<body>
  <div class="d-flex container w-100 mt-5 justify-content-center" style="margin-bottom: 220px">
    <form action="" method="post" enctype="multipart/form-data">
      <label class="form-label mt-1">Логин</label>
      <input type="text" class="form-control" name="login" placeholder="Введите свой логин">
      <label class="form-label mt-1">Почта</label>
      <input type="email" class="form-control" name="email" placeholder="Введите адрес своей почты">
      <label class="form-label mt-1">Пароль</label>
      <input type="password" class="form-control" name="password" placeholder="Введите пароль">
      <label class="form-label mt-1">Подтверждение пароля</label>
      <input type="password" class="form-control" name="password_confirm" placeholder="Подтвердите пароль">
      <button class="btn btn-primary mt-1 w-100" type="submit">Зарегистрироваться</button>
      <p>
        У вас уже есть аккаунт? - <a href="../php/auth.php">авторизируйтесь</a>!
      </p>
    </form>
  </div>

</body>

</html>