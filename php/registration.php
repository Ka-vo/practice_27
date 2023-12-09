<?php

namespace php;

session_start();

require_once 'service/db.php';

use service\Db;
use PDO;
use Exception;

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
        passward varchar(100) NOT NULL,
        roles varchar(100) NOT NULL
  )';

      return $dbh->exec($sql);
    } else {
      return 'Having connection problems, contact the administrator';
    }
  }

  public static function create_data()
  {
    $dbh = self::dbconn();
    if (!empty($_POST['login'])) {
      $LOGIN = $_POST['login'];
    }
    if (!empty($_POST['email'])) {
      $EMAIL = $_POST['email'];
    }
    //var_dump($_POST);
    if (!empty($_POST['password'])) {
      $PASSWARD = $_POST['password'];
    }
    $CONFIRMPASS  = $_POST['password_confirm'];
    $hash = password_hash($PASSWARD, PASSWORD_DEFAULT);
    $sql = 'INSERT INTO users(login, email, passward, roles) VALUES(:login, :email, :password, :roles)';
    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':login',  $LOGIN);
    $stmt->bindValue(':email', $EMAIL);
    $stmt->bindValue(':password', $hash);
    $stmt->bindValue(':roles', "User");

    if ((isset($_POST["login"])) && (isset($_POST["password"]))) {
      if ((empty($result)) && $CONFIRMPASS == $PASSWARD) {
        $stmt->execute();
        header("Location:php/Auth.php");
      } else {
        echo 'Такой логин уже существует';
      }
    } else {
      echo 'Необходимо заполнить форму';
    }
  }
}

class VKAuth
{
  public function authVK()
  {

    $clientId     = '25';
    $clientSecret = 'genaBukin';
    $redirectUri  = 'http://localhost:8000/php/main.php';

    $params = array(
      'client_id'     => $clientId,
      'redirect_uri'  => $redirectUri,
      'response_type' => 'code',
      'v'             => '5.126',

      'scope'         => 'photos,offline',
    );
    $params = array(
      'client_id'     => $clientId,
      'client_secret' => $clientSecret,
      'code'          => $_GET['code'],
      'redirect_uri'  => $redirectUri
    );

    if (!$content = @file_get_contents('https://oauth.vk.com/access_token?' . http_build_query($params))) {
      $error = error_get_last();
      throw new Exception('HTTP request failed. Error: ' . $error['message']);
    }

    $response = json_decode($content);

    if (isset($response->error)) {
      throw new Exception('При получении токена произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
    }

    $token = $response->access_token;
    $expiresIn = $response->expires_in;
    $userId = $response->user_id;

    $_SESSION['token'] = $token;
  }
}

$FormReg = new Registration();
echo $FormReg->create_data();
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
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
      <a class="btn btn-primary mt-1 w-100" href="http://oauth.vk.com/authorize?' . http_build_query($params) . '">Авторизоваться через VK</a>
      <p>
        У вас уже есть аккаунт? - <a href="/php/Auth.php">авторизируйтесь</a>!
      </p>
    </form>
  </div>

</body>

</html>