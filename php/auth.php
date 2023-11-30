<?php

namespace php;

require_once 'service/db.php';

use service\Db;
use PDO;


class Auth

{
  public static function login()
  {
    $dbh = Db::get();
    $LOGIN = $_POST['login'];
    $PASSWARD = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ((!empty($LOGIN)) && (!empty($PASSWARD))) {
      $res = $dbh->query("SELECT login, passward FROM users WHERE login = '$LOGIN'");

      $result = $res->Fetch(PDO::FETCH_ASSOC);

      if (!password_verify($_POST["password"], $result["passward"])) {
        return false;
      } else {
        return true;
      }
    }
  }
}
$account = new Auth();

if ($account->login()) {
  header("Location:main.php");
}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Авторизация и регистрация</title>
  <link rel="stylesheet" href="/assets/css/reset.css">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
  <div class="d-flex container w-100 mt-5 justify-content-center" style="margin-bottom: 220px">
    <form action="" method="post">
      <label class="form-label">Логин</label>
      <input class="form-control" type="text" name="login" placeholder="Введите свой логин">

      <label class="form-label mt-1">Пароль</label>
      <input class="form-control mt-1" type="password" name="password" placeholder="Введите пароль">

      <button class="btn btn-primary mt-1 w-100" type="submit">Войти</button>

      <p>
        У вас нет аккаунта? - <a href="./registration.php">зарегистрируйтесь</a>!
      </p>
    </form>
  </div>

</body>

</html>