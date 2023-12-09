<?php

namespace service;

use PDO;

class Db
{

  public static function connect()
  {

    $dbuser = 'admin';
    $dbpass = 'root';
    $host = 'db';
    $dbname = 'postgres';
    $dbh = new PDO("pgsql:host=localhost;dbname=$dbname", $dbuser, $dbpass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    return $dbh;
  }

  public static function get()
  {
    if (null <> self::connect()) {
      $conn = self::connect();
    }

    return $conn;
  }


  protected function __construct()
  {
  }
}

class Roles
{
  public static function user()
  {
    $User = 'User';
    return  $User;
  }
  public static function vkuser()
  {
    $VKUser = 'VKUser';
    return  $VKUser;
  }
}
