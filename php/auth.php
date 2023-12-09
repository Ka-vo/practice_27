<?php

namespace php;

session_start();

require_once 'service/db.php';

require_once '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use service\Db;
use PDO;
use Exception;
use service\Roles;

$log = new Logger('LOGGER');
$log->pushHandler(new StreamHandler('mylog.log', Logger::DEBUG));
$log->debug('Предупреждение');


class Auth

{
  public static function token()
  {
    $token = hash('gost-crypto', random_int(0, 999999));
    $_SESSION["CSRF"] = $token;
    return $token;
  }
  public static function arr()
  {
    $dbh = Db::get();
    if (!empty($_POST['login'])) {
      $LOGIN = $_POST['login'];
    }
    if (!empty($_POST['password'])) {
      $PASSWARD = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if (self::token() == $_SESSION["CSRF"]) {
      if ((!empty($LOGIN)) && (!empty($PASSWARD))) {
        $parameterLogin = strval($LOGIN);
        $res = $dbh->query("SELECT login, passward, roles FROM users WHERE login = '$parameterLogin'");

        $result = $res->Fetch(PDO::FETCH_ASSOC);
      }
      return $result;
    }
  }
  public static function login()
  {
    $dbh = Db::get();
    if (!empty($_POST['login'])) {
      $LOGIN = $_POST['login'];
    }
    if (!empty($_POST['password'])) {
      $PASSWARD = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $result = self::arr();
    if (!password_verify($_POST["password"], $result["passward"])) {
      $_SESSION['username'] = $result['login'];

      return false;
    } else {
      return true;
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


$account = new Auth();
$token = $account->token();
$role = $account->arr();
if ($role["roles"] == Roles::user()) {
  $_SESSION['role'] = $role["roles"];
} else {
  $_SESSION['role'] = Roles::vkuser();
}

if ($account->login()) {
  header("Location:Main.php");
} else {
  echo 'Неверный логин или пароль';
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
      <input type="hidden" name="token" value="<?php $token ?>"><br />
      <button class="btn btn-primary mt-1 w-100" type="submit">Войти</button>
      <a class="btn btn-primary mt-1 w-100" href="http://oauth.vk.com/authorize?' . http_build_query($params) . '">Авторизоваться через VK</a>

      <p>
        У вас нет аккаунта? - <a href="./Registration.php">зарегистрируйтесь</a>!
      </p>
    </form>
  </div>

</body>

</html>

<?php
